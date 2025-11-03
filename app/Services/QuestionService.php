<?php

namespace App\Services;

use App\Http\Requests\Question\StoreRequest;
use App\Models\Category;
use App\Models\Question;
use App\Models\QuestionComments;
use Illuminate\Support\Facades\Log;

Class QuestionService
{
    private static $model = Question::class;

    public static function getList($filter = [], $select = ['*'], $limit = 10)
    {
        //        cache
        if (empty($filter)) {
            return false;
        }

        $limit = $limit > 0 && $limit < 100 ? $limit : 10;

        $query = self::$model::query();

        foreach ($filter as $key => $item) {
            $query->where($key, $item);
        }

        $result = $query->paginate($limit, $select);

        return $result;
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
        return self::$model::query()->where('id', $data['question_id'])
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

        [$data, $error] = $this->prepareStoraData($request);

        if (!$data){
            return [false, $error];
        }

        return [ Question::create($data), null ];
    }

    private function prepareStoraData(StoreRequest $request)
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
                    return [null, ['File not valid']];
                }
            }

            if (isset($data['category'])){
                $category = Category::getElement($data['category']);
                $data['category_id'] = $category?->id ?? 0;
                unset($data['category']);
            }
        } catch (\Throwable $th) {
            Log::error('Prepare data error: ' . $th->getMessage());
            return [null, 'Ошибка обработки данных: ' . $th->getMessage()];
        }

        return [$data, null];
    }
}