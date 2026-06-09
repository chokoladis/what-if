<?php

namespace App\Services\AI\Gemini;

use App\Exceptions\FileSaveException;
use App\Exceptions\FileValidationException;
use App\Exceptions\Integration\AIWorkException;
use App\Interfaces\AI\AIClientContract;
use App\Interfaces\AI\ValidatorAvatarContract;
use App\Models\File;
use App\Models\Setting;
use App\Models\TempFile;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AvatarValidatorService implements ValidatorAvatarContract
{
    public function __construct(
        private AiClientContract $AIClient,
    )
    {
    }

    public function isSetOn(): bool
    {
        $res = Setting::query()->where('name', 'gemini_validate_user_photos')->first();
        if ($res) {
            return filter_var($res->value, FILTER_VALIDATE_BOOLEAN);
        }

        return false;
    }

    /**
     * @param TempFile|File $file
     * @throws FileSaveException
     * @throws AIWorkException
     * @throws ConnectionException
     */
    public function isContentFileLegal(TempFile|File $file) : true
    {
        $disk = Storage::disk('public');
        $chankPath = (get_class($file) === TempFile::class ? 'temp' : $file->relation) . '/' . $file->path;

        if (!$disk->exists($chankPath)) {
            throw new FileSaveException(__('entities.integrations.file_not_found'));
        }

        $fullPath = $disk->path($chankPath);
        $content = file_get_contents($fullPath);
        $mime = mime_content_type($fullPath);

        if ($content === false || $mime === false) {
            throw new FileSaveException('Не получилось достать контент из файла');
        }

        $base64data = base64_encode($content);

        $response = $this->AIClient->sendRequest([
            'contents' => [
                'parts' => [
                    [
                        'inline_data' => [
                            'mime_type' => $mime,
                            'data' => $base64data
                        ]
                    ],
                    [
                        'text' => 'Is the attached image legitimate. Return response next format - (bool); (string:*max 70 chars*|null). Example - true; , or false;is picture 18+.'
                    ]
                ],
            ]
        ]);

        return $this->handleResponse($response);
    }

    /**
     * @param mixed $responseData
     * @throws AIWorkException
     * @throws FileValidationException
     */
    protected function handleResponse(mixed $responseData): true // сделать интерфейсом ?
    {
        if ($responseData['error']) {
            Log::error(__CLASS__, [$responseData['error']]);
            throw new AIWorkException(__CLASS__ . ', error status - ' . $responseData['error']['status']);
        }

        $content = current($responseData['candidates'])['content'];
        $firstPart = current($content['parts']);
        $jsonResult = $firstPart['text'];

        if (stripos($jsonResult, ';') === false) {
            Log::debug(__CLASS__ . ', incorrect format', [$jsonResult]);
            throw new AIWorkException(__CLASS__ . ', incorrect format');
        } else {
            [$isLegal, $error] = explode(';', $jsonResult);

            $isLegal = filter_var($isLegal, FILTER_VALIDATE_BOOLEAN);

            if (!$isLegal) {
                throw new FileValidationException($error);
            } else {
                return true;
            }
        }
    }
}