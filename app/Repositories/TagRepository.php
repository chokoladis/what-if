<?php

namespace App\Repositories;

use App\Models\Tag;
use Illuminate\Support\Facades\Cache;

class TagRepository
{
    public function getAll()
    {
        return Cache::remember('tags_all', 3600, function () {
            return Tag::all();
        });
    }
}