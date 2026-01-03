<?php

namespace App\Services;

use App\Http\Requests\Search\IndexRequest;
use App\Interfaces\Models\SearchableInterface;
use App\Models\Category;
use App\Models\Question;
use App\Models\QuestionComments;
use \Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Log;

Class SearchService
{
    const DEFAULT_LIMIT = 10;
    const MAX_LIMIT = 100;


//    todo suggest search - limit 3 or 5
    public function prepareData(IndexRequest $request)
    {
        $data = $request->validated();

        if (isset($data['q'])){
            $filter = [
                'title' => ['title', 'LIKE', '%' . $data['q'] . '%'],
            ];
        }

        if (isset($data['limit'])) {
            $limit = $data['limit'] > 0 && $data['limit'] < self::MAX_LIMIT ? $data['limit'] : self::DEFAULT_LIMIT;
        }

        if (isset($data['sort'])){
            if ($data['sort'] === 'popular'){
                $sortBy = 'statistics.views';
                $order = 'desc';
            } else {
                [$sortBy, $order] = explode(',', $data['sort']);
            }
        }

        $sortBy = $sortBy ?? 'id';
        $order = $order ?? 'desc';
        $limit = $limit ?? self::DEFAULT_LIMIT;
        $filter = $filter ?? [];
//        submit on btn

        return [$filter, [$sortBy, $order], $limit];
    }

//    private function getByTrigram()
//    {
//
//    }
}