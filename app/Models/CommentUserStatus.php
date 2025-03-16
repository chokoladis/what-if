<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommentUserStatus extends Model
{
    use HasFactory;

    public $guarded = [];

    public static function getForCurrentUser($commentId)
    {
        // cache
        return CommentUserStatus::query()
            ->where('comment_id', $commentId)
            ->where('user_id', auth()->id())
            ->get('status')
            ->first();
    }
}
