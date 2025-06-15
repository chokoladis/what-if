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

        $fileContent = 'data:'.$mimeType.';base64,'.$base64data;

        $requestData = [ 'contents' =>
            [
                'parts' => [
                    'inline_data' => [
                        'mime_type' => $mimeType,
                        'data' => $fileContent
                    ]
                ],
                'text' => 'Законное ли прикрепленное изображение. Ответ в виде json со значениями success(bool), result(string). Если законно, верни success - true, result - пустота, иначе success - false, result - *дай пояснение для вывода ошибки*'
            ]
        ];

        return $this->request($requestData);
    }
}