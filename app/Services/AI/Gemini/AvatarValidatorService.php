<?php

namespace App\Services\AI\Gemini;

use App\DTO\Errors\CommonError;
use App\Exceptions\FileSaveException;
use App\Models\File;
use App\Models\Setting;
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

    public function isContentFileLegal(File $file)
    {
        if (!$this->isSetOn()) {
            return [true, null];
        }

        $disk = Storage::disk('public');
        $chankPath = $file->relation . '/' . $file->path;

        if (!$disk->exists($chankPath)) {
            throw new FileSaveException(__('entities.integrations.file_not_found'), 'file_not_found');
        }

        $fullPath = $disk->path($chankPath);
        $mimeType = mime_content_type($fullPath);
        $base64data = base64_encode(file_get_contents($fullPath));

        $requestData = ['contents' => [
            'parts' => [
                [
                    'inline_data' => [
                        'mime_type' => $mimeType,
                        'data' => $base64data
                    ]
                ],
                [
                    'text' => 'Is the attached image legitimate. Return response next format - (bool); (string:*max 70 chars*|null). Example - true; , or false;is picture 18+.'
                ]
            ],
        ]];

        return $this->request($requestData);
    }
}