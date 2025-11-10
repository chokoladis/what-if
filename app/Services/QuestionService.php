<?php

namespace App\Services;

use App\DTO\Errors\CommonError;
use App\DTO\Errors\ValidationError;
use App\Http\Requests\Question\StoreRequest;
use App\Models\Category;
use App\Models\Question;
use App\Models\QuestionComments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

Class QuestionService
{
    const DEFAULT_LIMIT = 10;
    const MAX_LIMIT = 100;
    private static $model = Question::class;

    public static function getList(
        array $filter = [],
        array $sort = [],
        int $limit = 10)
    {
        //        cache
        if (empty($filter)) {
            return false;
        }

        $limit = $limit > 0 && $limit < self::MAX_LIMIT ? $limit : self::DEFAULT_LIMIT;

        $query = self::$model::query();

        foreach ($filter as $key => $item) {
            if (is_array($item)){
                [$col, $operator, $value] = $item;
                $query->where($col, $operator, $value);
            } else {
                $query->where($key, $item);
            }
        }

        if (!empty($sort)) {
            $query->orderBy($sort[0], $sort[1]);
        }

//        $select
        return $query->paginate($limit);
    }

    public function getByCode(string $code)
    {
        $question = $this->model::where('code', $code)->first();
    }

    public function isCommentContains(array $data)
    {
        return QuestionComments::query()
            ->where('question_id', $data['question_id'])
            ->where('comment_id', $data['comment_id'])
            ->first('id');
    }

    public function setRightComment($data)
    {
        return self::$model::query()
            ->where('id', $data['question_id'])
            ->update(['right_comment_id' => $data['comment_id']]);
    }

    public function store(StoreRequest $request)
    {
        $question = Question::query()
            ->where('title', $request->get('title'))
            ->first();
        if ($question){
            return [$question, null];
        }

        [$data, $error] = $this->prepareStoreData($request);

        if (!$data){
            return [false, $error];
        }

        return [ Question::create($data), null ];
    }

    private function prepareStoreData(StoreRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();//user()->id; // заглушка
        $data['active'] = $request->user()->can('isAdmin', auth()->user());

        try {
            if ($request->has('img') && $img = $request->file('img')) {
                if ($img->isValid()) {
                    // todo transactions
                    $res = FileService::save($img, 'questions');
                    $data['file_id'] = $res['id'];
                    unset($data['img']);
                } else {
                    // todo translate
                    return [null, [ new ValidationError('File not valid', 'img', 'img_not_valid')]];
                }
            }

            if (isset($data['category'])){
                if ($category = Category::getElement($data['category'])){
                    $data['category_id'] = $category->id;
                }
                unset($data['category']);
            }
        } catch (\Throwable $th) {
            Log::error('Prepare data error: ' . $th->getMessage());
            return [null, new CommonError('Ошибка обработки данных: ' . $th->getMessage())];
        }

        return [$data, null];
    }

    public static function getActive(Request $request)
    {
//        todo css pagination
        //cache
        return Question::where('active', true)
            ->paginate(10)
            ->withQueryString();
    }
}