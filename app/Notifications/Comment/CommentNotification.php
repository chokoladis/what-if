<?php

namespace App\Notifications\Comment;

use App\Interfaces\Services\UniqueDataNotifyInterface;
use App\Models\Comment;
use App\Models\User;
use App\Notifications\BaseNotification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class CommentNotification extends BaseNotification implements UniqueDataNotifyInterface
{
    /**
     * Create a new notification instance.
     */
    public function __construct(
        private User    $voter,
        private Comment $comment,
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
                'title' => safeVal($this->comment->question->getShortTitle()),
                'url' => route('questions.detail', $this->comment->question->code),
            ],
            'comment' => [
                'id' => $this->comment->id,
                'text' => $this->comment->getShortText()
            ]
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        $url = route('questions.detail', $this->comment->question->code);
        $message = sprintf(
            'Под вашим вопросом - <a href="%s">%s</a> оставили комментарий - <b>"%s"</b>',
            $url,
            safeVal($this->comment->question->getShortTitle()),
            safeVal($this->comment->getShortText()),
        );

        return new BroadcastMessage([
            'message' => $message,
        ]);
    }

    public function getUniqueData(): array
    {
        return [
            'from_user->id' => $this->voter->id,
            'comment->id' => $this->comment->id
        ];
    }
}
