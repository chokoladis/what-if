<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class QuestionComments extends Model
{
    use HasFactory;

    public $guarded = [];

    public function comment(): HasOne
    {
        return $this->HasOne(Comment::class, 'id', 'comment_id');
    }
}
