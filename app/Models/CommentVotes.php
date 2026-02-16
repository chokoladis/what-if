<?php

namespace App\Models;

use App\Enums\Vote;
use App\Notifications\Comment\VoteNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommentVotes extends Model
{
    use HasFactory;

    public $guarded = [];

    public function getTable()
    {
        return 'comment_votes';
    }

    public static function getForCurrentUser($commentId)
    {
        // cache
        return CommentVotes::query()
            ->select('vote')
            ->where('comment_id', $commentId)
            ->where('user_id', auth()->id())
            ->first();
    }

    static function boot()
    {
        parent::boot();

        static::updated(function ($item) {

        });

        static::created(function ($item) {

            if (Vote::from($item->vote) === Vote::LIKE) {

                $notification = new VoteNotification($item->user, $item->comment);
                if (!VoteNotification::isExists($notification)) {
                    $item->comment->user->notify($notification);
                }
            }
        });
    }

    public function comment(): BelongsTo
    {
        return $this->belongsTo(Comment::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
