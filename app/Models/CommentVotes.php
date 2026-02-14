<?php

namespace App\Models;

use App\Enums\NotificationType;
use App\Enums\Vote;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;

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

            if (Vote::from($item->vote) === Vote::LIKE) {
            }
        });

        static::created(function ($item) {

            if (Vote::from($item->vote) === Vote::LIKE) {

//                $url = route('questions.detail', $this->comment->question->code);
//                $message = sprintf('Ваш комментарий - <a href="%s">%s</a> лайкнул пользователь - %s',
//                    $url,
//                    safeVal($this->comment->getShortText()),
//                    safeVal($this->user->name)
//                );
            }
        });
    }

    public function comment(): BelongsTo
    {
        return $this->belongsTo(Comment::class);
    }
}
