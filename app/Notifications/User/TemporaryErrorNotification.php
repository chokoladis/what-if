<?php

namespace App\Notifications\User;

use App\Notifications\BaseNotification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class TemporaryErrorNotification extends BaseNotification
{
    const MESSAGE = 'Техническая ошибка с проверкой аватара (%s), пожалуйста попробуйте позже';

    /**
     * Create a new notification instance.
     */
    public function __construct(
        private string $fileName,
    )
    {
    }

    /** @return array<string, string> */
    public function toArray(object $notifiable): array
    {
        return [
            'text' => sprintf(self::MESSAGE, $this->fileName)
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'message' => sprintf(self::MESSAGE, $this->fileName)
        ]);
    }
}
