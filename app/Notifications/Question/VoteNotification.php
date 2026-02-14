<?php

namespace App\Notifications\Question;

use App\Models\Question;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class VoteNotification extends Notification
{
    use Queueable;

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
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['broadcast', 'database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
//        check unique
        $url = route('questions.detail', $this->question->code);
        $message = sprintf('Ваш вопрос - <a href="%s">%s</a> лайкнул пользователь - %s',
            $url,
            safeVal($this->question->getShortTitle()),
            safeVal($this->voter->name)
        );

        return [
            'from_user_id' => $this->voter->id,
            'message' => $message,
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
//        $notifiable - model user

        $url = route('questions.detail', $this->question->code);
        $message = sprintf(
            'Ваш вопрос - <a href="%s">%s</a> лайкнул пользователь - %s',
            $url,
            safeVal($this->question->getShortTitle()),
            safeVal($this->voter->name)
        );

        return new BroadcastMessage([
            // Кто поставил лайк
            'from' => $this->voter->id,
            // Кому предназначено уведомление (владелец вопроса)
            'to' => $notifiable->id,
            'message' => $message,
        ]);
    }
}
