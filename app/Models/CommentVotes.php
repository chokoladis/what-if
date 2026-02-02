<?php

namespace App\Models;

use App\Enums\NotificationType;
use App\Enums\Vote;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommentVotes extends Model
{
    use HasFactory;

    public $guarded = [];

    public function getTable()
    {
        return 'comment_user_votes';
    }

    public static function getForCurrentUser($commentId)
    {
        // cache
        return CommentVotes::query()
            ->select('votes')
            ->where('comment_id', $commentId)
            ->where('user_id', auth()->id())
            ->first();
    }

    static function boot()
    {
        parent::boot();

        static::creating(function ($item) {
            if (Vote::LIKE === $item->vote) {
                Notification::create([
                    'user_id' => $item->user_id,
                    'entity_id' => $item->id,
                    'entity' => __CLASS__,
                    'type' => NotificationType::COMMENT_LIKED,
                ]);
            }
        });
    }
}
