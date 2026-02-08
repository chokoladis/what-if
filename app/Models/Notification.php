<?php

namespace App\Models;

use App\Enums\NotificationType;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function question()
    {
        return $this->belongsTo(Question::class, 'entity_id');
    }

    public function comment()
    {
        return $this->belongsTo(Comment::class, 'entity_id');
    }

    public function toMessage() : string
    {
        $type = NotificationType::from($this->type);

        if ($type === NotificationType::QUESTION_LIKED) {
            // todo ссылку на пользователя
            $url = route('questions.detail', $this->question->code);
            $message = sprintf('Ваш вопрос - <a href="%s">%s</a> лайкнул пользователь - %s',
                $url,
                safeVal($this->question->getShortTitle()),
                safeVal($this->user->name)
            );
        } else if ($type === NotificationType::COMMENT_LIKED) {
            $url = route('questions.detail', $this->comment->question->code);
            $message = sprintf('Ваш комментарий - <a href="%s">%s</a> лайкнул пользователь - %s',
                $url,
                safeVal($this->comment->getShortText()),
                safeVal($this->user->name)
            );
        }

        return $message;
    }
}
