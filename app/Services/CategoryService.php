<?php

declare(strict_types=1);

namespace App\Services;


use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class CategoryService
{
    const CACHE_KEY_LEVEL0 = 'category_level_0';

    public static function getCategoriesLevel0(): \Illuminate\Support\Collection
    {
        return Cache::remember(self::CACHE_KEY_LEVEL0, now()->addDay(), function () {
            return Category::active()
                ->where('level', 0)
                ->with('file')
                ->get(['id', 'code', 'title', 'file_id']);
        });
    }

    public function getByCode(string $code): ?Category
    {
        /** @var ?Collection<int|string, Category> $categories */
        $categories = Cache::get(self::CACHE_KEY_LEVEL0);
        // todo check time loading with cache collection
        if ($categories?->isNotEmpty()) {
            /** @var ?Category $category */
            $category = $categories->first(
                function (Category $category) use ($code) {
                    return $category->code === $code;
                });

            if ($category)
                return $category;
        }

        return Category::getByCode($code);
    }
}