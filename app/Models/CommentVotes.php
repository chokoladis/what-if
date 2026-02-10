<?php

namespace App\Models;

use App\Enums\NotificationType;
use App\Enums\Vote;
use App\Services\NotificationService;
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
        $notifyService = new NotificationService();

        parent::boot();

        static::updated(function ($item) use ($notifyService) {

            if (Vote::from($item->vote) === Vote::LIKE) {
                $notifyService->vote(NotificationType::QUESTION_LIKED, $item);
            }
        });

        static::created(function ($item) use ($notifyService) {

            if (Vote::from($item->vote) === Vote::LIKE) {
                $notifyService->vote(NotificationType::COMMENT_LIKED, $item);
            }
        });
    }

    public function comment(): BelongsTo
    {
        return $this->belongsTo(Comment::class);
    }
}
