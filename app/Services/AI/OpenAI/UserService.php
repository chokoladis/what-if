<?php

namespace App\Services\AI\OpenAI;

use App\Models\File;

class UserService extends BaseService
{
    public function isContentFileLegal(File $file)
    {
        $fullPath = $_SERVER['DOCUMENT_ROOT'] . '/storage/' . $file->relation . '/' . $file->path;

        if (!file_exists($fullPath)) {
            return [false, new \Error(__('entities.integrations.file_not_found'), 'file_not_found')];
        }

        $mimeType = mime_content_type($fullPath);
        $base64data = base64_encode(file_get_contents($fullPath));

        $fileContent = 'data:' . $mimeType . ';base64,' . $base64data;

        $requestData = [
            'role' => 'user',
            'content' => [
                [
                    'type' => 'input_text',
                    'text' => 'Законное ли прикрепленное изображение, не нарушает ли какие либо законы. Ответ в виде json со значениями success(bool), result(string). Если законно, отдай в success - true, в result запиши пустоту, иначе success - false, result - *дай пояснение для вывода ошибки*',
                ],
                [
                    'type' => 'input_image',
                    'image_url' => $fileContent
                ]
            ]
        ];

        return $this->request([
            'input' => [$requestData]
        ]);
    }
}