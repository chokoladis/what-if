<?php

namespace App\Repositories;

use App\Models\Category;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Laravel\Scout\Builder;
use Meilisearch\Endpoints\Indexes;

class CategoryRepository extends Repository
{
    public function getSearchBuilder(mixed $data, mixed $filters = []): Builder
    {
        return $this->model::search($data['q'], function (Indexes $meilisearch, string $query, array $options) use ($filters) {
            $options['filter'] = $filters;
            $options['sort'] = ['count_question:desc'];
            return $meilisearch->search($query, $options);
        });
    }

    public function getActive() : Collection
    {
        return Cache::remember('category_active', 86400, function () {
            return Category::active()->with('file')->get();
        });
    }
}
