<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\Errors\ValidationError;
use App\Models\Feedback;

class FeedbackService
{
    /**
     * @param array<string, ?string> $data
     * @return array<int, Feedback|ValidationError[]|null>
     */
    public function store(array $data)
    {
        $errors = [];
        // if ($data['email'])

        if (!empty($data['phone'])) {
            $data['phone'] = getNumbers($data['phone']);

            if (strlen($data['phone']) !== 11) {
                $errors[] = new ValidationError(
                    'Ошибка заполнения телефона',
                    'phone',
                );
            }
        }

        if (!empty($errors)) {
            return [null, $errors];
        }

        return [
            Feedback::firstOrCreate([
                //todo составной индекс
                'email' => $data['email'],
                'comment' => $data['comment']
            ], $data),
            null
        ];
    }
}
