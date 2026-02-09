<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

//todo search where used old name
class UserTags extends Model
{
    use HasFactory;

    public $guarded = [];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function tag()
    {
        return $this->hasOne(Tag::class, 'id', 'tag_id');
    }
}
