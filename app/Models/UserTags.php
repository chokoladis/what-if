<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

//todo search where used old name
class UserTags extends Model
{
    use HasFactory;

    public $guarded = [];

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function tag(): HasOne
    {
        return $this->hasOne(Tag::class, 'id', 'tag_id');
    }

//    todo drop in boot methods
}
