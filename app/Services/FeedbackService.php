<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\Errors\ValidationError;
use App\Models\Feedback;

class FeedbackService
{
    /**
     * @param array<string, ?string> $data
     * @return array<int, Feedback|ValidationError|null>
     */
    public function store(array $data)
    {
        // if ($data['email'])

        if (!empty($data['phone'])) {
            $data['phone'] = getNumbers($data['phone']);

            if (strlen($data['phone']) < 4 || strlen($data['phone']) > 15) {
                return [null, new ValidationError(
                    'phone',
                    'Некорректная длина номера телефона',
                )];
            }
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
