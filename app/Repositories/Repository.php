<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Laravel\Scout\Builder;
use Meilisearch\Endpoints\Indexes;

abstract class Repository
{
    const DEFAULT_PER_PAGE = 10;

    public function __construct(
        protected      $model,
        protected bool $getFromDB = false
    )
    {
        if (!is_subclass_of($this->model, Model::class)) {
            throw new \Exception("Model class must be an instance of Illuminate\Database\Eloquent\Model");
        }
    }

    public function getSearchBuilder(array $data, ?array $filters = []): Builder
    {
        return $this->model::search($data['q'], function (Indexes $meilisearch, string $query, array $options) use ($filters) {
            $options['filter'] = $filters;
            return $meilisearch->search($query, $options);
        });
    }

    public function searchWithPaginate(array $data, ?array $filters = []): LengthAwarePaginator
    {
        return $this->getSearchBuilder($data, $filters)->paginateRaw(static::DEFAULT_PER_PAGE);
    }
}