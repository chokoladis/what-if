<?php

namespace App\Services\AI\Gemini;

use App\DTO\Errors\CommonError;
use App\Exceptions\FileSaveException;
use App\Exceptions\Integration\AIWorkException;
use App\Models\File;
use App\Models\Setting;
use App\Models\TempFile;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Storage;

class AvatarValidatorService extends BaseService
{
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
     * @return array{bool, CommonError|null}
     * @throws FileSaveException
     * @throws AIWorkException
     * @throws ConnectionException
     */
    public function isContentFileLegal(TempFile|File $file)
    {
        $disk = Storage::disk('public');
        $chankPath = ( get_class($file) === TempFile::class ? 'temp' : $file->relation ) . '/' . $file->path;

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

        return $this->sendRequest([
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
    }
}