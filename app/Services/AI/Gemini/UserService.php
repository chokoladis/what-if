<?php

namespace App\Services\AI\Gemini;

use App\Models\File;
use App\Services\AI\Gemini\BaseService;

class UserService extends BaseService
{
    public function isContentFileLegal(File $file)
    {
        $fullPath = $_SERVER['DOCUMENT_ROOT'].'/storage/'.$file->relation.'/'.$file->path;

        if (!file_exists($fullPath)){
            return [false, new \Error(__('entities.integrations.file_not_found'))];
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