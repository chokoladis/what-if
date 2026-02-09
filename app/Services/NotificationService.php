<?php

namespace App\Services;

use App\Enums\NotificationType;
use App\Events\Broadcast\Comment\Vote as CommentVote;
use App\Events\Broadcast\Question\Vote as QuestionVote;
use App\Models\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

final class NotificationService
{
    public function vote(NotificationType $type, Model $modelVote)
    {
//        todo middleware or base service / magic method ?
        if (strtolower(config('notification.status')) === 'off') {
            return;
        }

        if ($type === NotificationType::QUESTION_LIKED) {
            $entity = 'question';
        } else if ($type === NotificationType::COMMENT_LIKED) {
            $entity = 'comment';
        } else {
            throw new \Exception('err type');
        }

        if ((int)$modelVote->$entity->user_id === (int)auth()->id()) {
            return ;
        }

//        todo fix dublicate
        $notification = Notification::create([
            'user_id' => auth()->id(), //todo from user_id, to user_id ?
            'entity' => $entity,
            'entity_id' => $modelVote->$entity->id,
            'type' => $type->value,
        ]);

        if ($entity === 'question') {
            QuestionVote::dispatch($notification, $notification->toMessage());
        } else {
            CommentVote::dispatch($notification, $notification->toMessage());
        }
    }
}
