<?php

namespace App\Services\AI\Gemini;

use App\DTO\Errors\CommonError;
use App\Models\File;
use App\Models\Setting;
use App\Services\AI\Gemini\BaseService;
use App\Services\SettingService;

class UserService extends BaseService
{
    public function isSetOn(): bool
    {
        $res = Setting::query()->where('name', 'gemini_validate_user_photos')->first();
        if ($res){
            return filter_var($res->value, FILTER_VALIDATE_BOOLEAN);
        }

        return false;
    }

    public function isContentFileLegal(File $file)
    {
        if (!$this->isSetOn()){
            return [true];
        }

        $fullPath = $_SERVER['DOCUMENT_ROOT'].'/storage/'.$file->relation.'/'.$file->path;

        if (!file_exists($fullPath)){
            return [false, new CommonError(__('entities.integrations.file_not_found'), 'file_not_found')];
        }

        $mimeType = mime_content_type($fullPath);
        $base64data = base64_encode(file_get_contents($fullPath));

        $requestData = [ 'contents' => [
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