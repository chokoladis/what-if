<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Meilisearch\Endpoints\Indexes;

abstract class Repository
{
    const DEFAULT_PER_PAGE = 10;

    public function __construct(
        protected $model,
        protected bool $getFromDB = false
    )
    {
        if (!is_subclass_of($this->model, Model::class)) {
            throw new \Exception("Model class must be an instance of Illuminate\Database\Eloquent\Model");
        }
    }

    public function search(array $data, ?array $filters = []) : LengthAwarePaginator
    {
        $query = $this->model::search( $data['q'], function (Indexes $meilisearch, string $query, array $options) use ($filters) {
            $options['filter'] = $filters;
            return $meilisearch->search($query, $options);
        });

//        if ($this->getFromDB) {
//            return $query->paginate(self::DEFAULT_PER_PAGE);
//        } else {
            return $query->paginateRaw(static::DEFAULT_PER_PAGE);
//        }
    }
}