<?php

namespace App\Repositories;

use Laravel\Scout\Builder;
use Meilisearch\Endpoints\Indexes;

class CategoryRepository extends Repository
{
    public function getSearchBuilder(array $data, ?array $filters = []): Builder
    {
        return $this->model::search($data['q'], function (Indexes $meilisearch, string $query, array $options) use ($filters) {
            $options['filter'] = $filters;
            $options['sort'] = ['count_question:desc'];
            return $meilisearch->search($query, $options);
        });
    }
}
