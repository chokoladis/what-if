<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Tag;
use App\Models\UserTags;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\Cache;

class TagService
{
    public function getForUser(int $userId): Collection
    {
        return Cache::remember('user_tags_' . $userId, 86400, function () use ($userId) {
            return UserTags::query()->where('user_id', $userId)->get();
        });
    }

    public function getNotSelected(SupportCollection $tagIds): Collection
    {
        return Cache::remember(serialize('tags_not_selected_' . $tagIds->toJson()), 86400, function () use ($tagIds) {
            return Tag::query()->whereNotIn('id', $tagIds)->get();
        });
    }
}