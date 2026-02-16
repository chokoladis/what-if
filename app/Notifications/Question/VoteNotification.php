<?php

namespace App\Notifications\Question;

use App\Interfaces\Services\UniqueDataNotifyInterface;
use App\Models\Question;
use App\Models\User;
use App\Notifications\BaseNotification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class VoteNotification extends BaseNotification implements UniqueDataNotifyInterface
{

    /**
     * Create a new notification instance.
     */
    public function __construct(
        private User $voter,
        private Question $question,
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
            'from_user' => [
                'id' => $this->voter->id,
                'name' => safeVal($this->voter->name)
            ],
            'question' => [
                'id' => $this->question->id,
                'title' => safeVal($this->question->getShortTitle()),
                'url' => route('questions.detail', $this->question->code),
            ],
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        $url = route('questions.detail', $this->question->code);
        $message = sprintf(
            'Ваш вопрос - <a href="%s">%s</a> лайкнул пользователь - %s',
            $url,
            safeVal($this->question->getShortTitle()),
            safeVal($this->voter->name)
        );

        return new BroadcastMessage([
            'message' => $message,
        ]);
    }

    public function getUniqueData(): array
    {
        return [
            'from_user->id' => $this->voter->id,
            'question->id' => $this->question->id
        ];
    }
}
