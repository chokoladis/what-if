<?php

namespace App\Models;

use App\Enums\NotificationType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use HasFactory;
    use SoftDeletes;

    const DEFAULT_LIMIT = 10;

    public $guarded = [];

    public function getTable()
    {
        return 'comments';
    }

    public function user(): HasOne
    {
        return $this->HasOne(User::class, 'id', 'user_id');
    }

    public function isReply(): bool
    {
        return $this->parent && $this->parent->exists();
    }

    public function replies(): HasMany
    {
        return $this->HasMany(CommentsReply::class, 'comment_main_id', 'id')->orderBy('created_at');
    }

    public function parent(): HasOne
    {
        return $this->hasOne(CommentsReply::class, 'comment_reply_id', 'id');
    }

    public function getCountChilds($replies, int &$count = 0): int
    {
        if (empty($replies)) {
            return $count;
        } elseif (is_array($replies)) {
            $replies = CommentsReply::whereIn('comment_main_id', $replies)->orderBy('created_at')->get(['comment_reply_id', 'id']);

            if ($replies->isEmpty())
                return $count;
        }

        $commentsIds = [];
        foreach ($replies as $reply) {
            $commentsIds[] = $reply->comment_reply_id;
            $count++;
        }

        return $this->getCountChilds($commentsIds, $count);
    }


    public function getReplies($replies, &$result = [])
    {
        if (empty($replies)) {
            return $result;
        } elseif (is_array($replies)) {
            $replies = CommentsReply::whereIn('comment_main_id', $replies)->orderBy('created_at')->get();

            if ($replies->isEmpty())
                return $result;
        }

        $commentsIds = [];
        foreach ($replies as $reply) {
            $commentsIds[] = $reply->comment_reply_id;
            $result[] = $reply->reply;
        }
        // limit

        return $this->getReplies($commentsIds, $result);
    }

    public function getRating()
    {
        $commentVotes = new CommentVotes;

        return $this->newQuery()
            ->where('comments.id', $this->id)
            ->join($commentVotes->getTable().' as t_statuses', 'comments.id', '=', 'comment_id')
            ->selectRaw('SUM(t_statuses.vote) as rating')
            ->first();
    }

    public static function boot()
    {

        parent::boot();

        /**
         * Write code on Method
         *
         * @return response()
         */

        static::created(function ($item) {

            if (strtolower(config('notification.status')) !== 'off'){
                Notification::create([
                    'user_id' => $item->user_id,
                    'entity_id' => $item->id,
                    'entity' => __CLASS__,
                    'type' => $this->isReply() ? NotificationType::RESPONDED_TO_COMMENT : NotificationType::QUESTION_COMMENTED,
                ]);
            }
        });

    }

    public function getShortText()
    {
        return mb_strlen($this->text) > 20 ? mb_substr($this->text,0, 20).'...' : $this->text;
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }
}
