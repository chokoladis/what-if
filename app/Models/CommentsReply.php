<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CommentsReply extends Model
{
    use HasFactory;

    public $guarded = [];

    public function replyComment() : HasOne { //получаем ответы на первый коммент
        return $this->HasOne(Comment::class, 'id', 'comment_main_id');
    }
}
