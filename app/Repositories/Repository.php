<?php

namespace App\Repositories;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Builder;
use Meilisearch\Endpoints\Indexes;

abstract class Repository
{
    const DEFAULT_PER_PAGE = 10;

    public function __construct(
        protected mixed $model,
        protected bool $getFromDB = false
    )
    {
        if (!is_subclass_of($this->model, Model::class)) {
            throw new Exception("Model class must be an instance of Illuminate\Database\Eloquent\Model");
        }
    }

    public function searchWithPaginate(mixed $data, mixed $filters = []): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return $this->getSearchBuilder($data, $filters)->paginateRaw(static::DEFAULT_PER_PAGE);
    }

    public function getSearchBuilder(mixed $data, mixed $filters = []): Builder
    {
        return $this->model::search($data['q'], function (Indexes $meilisearch, string $query, array $options) use ($filters) {

            if (is_array($filters))
                $options['filter'] = $filters;

            return $meilisearch->search($query, $options);
        });
    }
}