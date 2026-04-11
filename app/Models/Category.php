<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class Category extends BaseModel
{
    use HasFactory;

//    use Searchable;

    const MAX_DEPTH = 3; //for check in add OR добавить на уровне добавления в базу ограничение

    /**
     * @param string|null $code
     * @return ?Category
     */
    public static function getByCode(?string $code): ?Category
    {
        return Cache::remember('category_' . $code, now()->addDay(), function () use ($code) {
            return Category::active()->where('code', $code)->with('file')->first();
        });
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
//            if (!is_numeric($category->file_id)){
//                $file = FileService::save($category->file_id, 'categories');
//                $category->file_id = $file->id;
//            }
            $category->code = Str::slug(Str::lower($category->title), '-');
            $category->level = !$category->parent ? 0 : $category->parent->level + 1;
        });

        static::updating(function ($category) {

//            todo
//            Log::debug('upd categori - '.$category->file_id);
//
//            if (!is_numeric($category->file_id)){
//                $file = FileService::save($category->file_id, 'categories');
//                $category->file_id = $file->id;
//            }
        });

        static::deleted(function ($item) {
            File::find($item->file_id)->delete();
        });
    }

    /**
     * @return array<int|null, mixed>
     */
    public static function getDaughtersCategories()
    {
        // todo rework,    cache
        $categories = Category::active()->orderBy('level', 'desc')->get();

        $arr = $res = [];

        foreach ($categories as $item) {
            $arr[$item->level][] = $item;
        }

        foreach ($arr as $level => $items) {

            /** @var Category $category */
            foreach ($items as $category) {

                $res[$level][$category->id] = [
                    'category' => $category,
                    'items' => $category->getDaughterCategories($category->level + 1, $category->id)
                ];
            }
        }

        return $res;
    }

    public function getDaughterCategories(int $catLevel, int $categoryParentId): mixed
    {
        return Category::query()
            ->where('level', $catLevel)
            ->where('parent_id', $categoryParentId)
            ->get()
            ->toArray();
        // imgs
    }

    public function getRouteKeyName()
    {
        return 'code';
    }

    public function scopeActive(): Builder
    {
        return Category::query()->where('active', true);
    }

    public function scopeSearch(Builder $query, string $title): Builder
    {
        return $query->where('title', 'LIKE', '%' . $title . '%')
            ->orWhere('code', 'LIKE', '%' . $title . '%');
    }

    /**
     * @return array<string, mixed>
     */
    public function toSearchableArray(): array
    {
        $count = $this->getCountQuestion();

        return [
            'title' => $this->title,
            'code' => $this->code,
            'parents' => $this->getParents(),
            'parent_id' => $this->parent_id,
            'created_at' => $this->created_at,
            'count_question' => $count
        ];
    }

    public function getCountQuestion(): int
    {
        return Question::search()
            ->where('category_id', $this->id)
            ->whereIn('category_list', $this->id)
            ->get()->count();
    }

    public function getParents(): mixed
    {
//        cache
        $arParents = [];
        $queryResult = null;

        while (true) {
            $parentId = !empty($queryResult) ? $queryResult->parent_id : $this->parent_id;
            $level = !empty($queryResult) ? $queryResult->level - 1 : $this->level - 1;
            if ($parentId) {
                if ($queryResult = $this->getParentById($parentId, $level)) {
                    $arParents[] = $queryResult;
                } else {
                    break;
                }
            } else {
                break;
            }
        }

        return $arParents;
    }

    public function getParentById(int $parentId, int $level): ?Category
    {
        return Category::query()
            ->where('active', 1)
            ->where('id', $parentId)
            ->where('level', $level)
            ->first();
    }

    public function shouldBeSearchable(): bool
    {
        return $this->active;
    }

    public function subcategories(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function parent(): HasOne
    {
        return $this->hasOne(Category::class, 'id', 'parent_id');
    }
}
