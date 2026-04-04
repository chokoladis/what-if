<?php

namespace App\Notifications\User;

use App\Models\File;
use App\Models\TempFile;
use App\Notifications\BaseNotification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class AvatarValidatedNotification extends BaseNotification
{

    /**
     * Create a new notification instance.
     */
    public function __construct(
        private TempFile|File $photo,
        private bool          $isCorrect,
    )
    {
    }


    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'file_id' => $this->photo->id,
            'filename' => $this->photo->name,
            'is_correct' => $this->isCorrect,
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        $message = sprintf(
            'Ваше фото (%s) было провалидировано AI, результат - %s',
            $this->photo->name,
            var_export($this->isCorrect, true)
        );

        return new BroadcastMessage([
            'message' => $message,
        ]);
    }
}
