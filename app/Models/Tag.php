<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class Tag extends Model
{
    use HasFactory;

    public $guarded = [];

    public $timestamps = false;

    public static function getAll() : Collection
    {
        return Cache::remember('tags', 3600, function () {
            return Tag::sorted()->get();
        });
    }

    public function scopeSorted(Builder $query) : void
    {
        $query->orderBy('sort');
    }
}
