<?php

namespace App\Models;

use App\Notifications\Comment\CommentNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

class Comment extends Model
{
    use HasFactory;
    use SoftDeletes;

    const DEFAULT_LIMIT = 10;

    public $guarded = [];

    public static function boot()
    {

        parent::boot();

        /**
         * Write code on Method
         *
         * @return response()
         */

        static::created(function ($item) {
            if (strtolower(config('notification.status')) !== 'off') {

                $notification = new CommentNotification($item->user, $item->comment);
                if (!CommentNotification::isExists($notification)) {
                    $item->comment->question->user->notify($notification);
                }
            }
        });

    }

    public function user(): HasOne
    {
        return $this->HasOne(User::class, 'id', 'user_id');
    }

    public function replies(): HasMany
    {
        return $this->HasMany(Comment::class, 'comment_main_id', 'id')
            ->orderBy('created_at');
    }

    public function parent(): HasOne
    {
        return $this->hasOne(Comment::class, 'id', 'comment_main_id');
    }

    public function getTotalCountChildren(Collection $children, int &$count = 0): int
    {
//        todo to clean sql?
        $count += $children->count();

        if ($children->isEmpty()) {
            return $count;
        } elseif ($children->isNotEmpty()) {
            $children = Comment::whereIn('comment_main_id', $children->pluck('id'))
                ->orderBy('created_at')
                ->get(['id']);

            if ($children->isEmpty())
                return $count;
        }

        return $this->getTotalCountChildren($children, $count);
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

        return $this->votes()
//            ->join($commentVotes->getTable().' as votes', 'comments.id', '=', 'comment_id')
            ->selectRaw('SUM(' . $commentVotes->getTable() . '.vote) as total_rating')
            ->first();
    }

    public function votes(): HasMany
    {
        return $this->hasMany(CommentVotes::class, 'comment_id', 'id');
    }

    public function getTable()
    {
        return 'comments';
    }

    public function getShortText()
    {
        return mb_strlen($this->text) > 20 ? mb_substr($this->text, 0, 20) . '...' : $this->text;
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }
}
