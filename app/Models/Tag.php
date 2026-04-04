<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Tag extends Model
{
    use HasFactory;

    public $guarded = [];

    public $timestamps = false;

    public static function getAll()
    {
        return Cache::remember('tags', 3600, function () {
            return Tag::sorted()->get();
        });
    }

    public function scopeSorted($query)
    {
        return $query->orderBy('sort');
    }
}
