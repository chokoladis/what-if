<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    public $guarded = [];

    public $timestamps = false;

    public function scopeSorted($query)
    {
        return $query->orderBy('sort');
    }

    public static function getAll()
    {
        return \Illuminate\Support\Facades\Cache::remember('tags', 3600, function () {
            return Tag::sorted()->get();
        });
    }
}
