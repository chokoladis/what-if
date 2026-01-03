<?php

namespace App\Models;

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
}
