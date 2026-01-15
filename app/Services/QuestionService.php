<?php

namespace App\Services;

use App\DTO\Errors\CommonError;
use App\DTO\Errors\ValidationError;
use App\Http\Requests\Question\StoreRequest;
use App\Models\Category;
use App\Models\Question;
use App\Models\QuestionComments;
use App\Models\QuestionTags;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class QuestionService
{
    const POPULAR_VIEW_RATIO = 0.2;
    const SORTS = [
        'id_desc' => 'new',
        'id_asc' => 'old',
        'popular' => 'popular'
    ];

    const ITEMS_TYPE_OUTPUT = [
        'simple', 'compact'
    ];

    private static $model = Question::class;

    public static function getList(
        array $filter = [],
        array $sort = [],
        int   $limit = 10
    )
    {
        if (empty($filter)) {
            return false;
        }

//        $key = implode('_', $filter).'_'.implode('_', $sort).'_'.$limit;
//        $query = Cache::remember('question_list_'.$key, 36000, function () use ($filter, $sort, $limit) {

        $query = self::$model::query();

        foreach ($filter as $key => $item) {
            if (is_array($item)) {
                [$col, $operator, $value] = $item;
                $query->where($col, $operator, $value);
            } else {
                $query->where($key, $item);
            }
        }

        if (!empty($sort)) {
            if (is_int(stripos($sort[0], 'statistics'))) {
                $col = explode('.', $sort[0]);
                $sortBy = $col[array_key_last($col)];

                $query->join('question_statistics', 'questions.id', '=', 'question_id');
                $query->orderBy($sortBy, $sort[1]);
            } else {
                $query->orderBy($sort[0], $sort[1]);
            }
        }

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
        if ($question) {
            return [$question, null];
        }

        [$data, $error] = $this->prepareStoreData($request);

        if (!$data) {
            return [false, $error];
        }

        $tags = $data['tags'];
        unset($data['tags']);

        $res = Question::create($data);

        if (!empty($res)) {
            $tags = Tag::query()->whereIn('name', $tags)->get('id');

            foreach ($tags as $tag) {
                QuestionTags::create([
                    'question_id' => $res->id,
                    'tag_id' => $tag->id
                ]);
            }

            return [$res, null];
        } else {
            return [false, new CommonError('Не удалось создать вопрос')];
        }
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
                    return [null, [new ValidationError('File not valid', 'img', 'img_not_valid')]];
                }
            }

            if (isset($data['category'])) {
                if ($category = Category::getElement($data['category'])) {
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

    public static function paginateWithFilter(Request $request)
    {
        $builder = Question::active();

        if ($request->tags){
            $builder->whereHas('tags', function ($query) use ($request) {
                return $query->whereIn('tags.name', $request->tags);
            });
        }

        if ($request->resolved) {
            $builder->whereNotNull('right_comment_id');
        }

        if ($request->categories) {
            // todo
            $builder->whereHas('category', function ($query) use ($request) {
               return $query->whereIn('categories.title', $request->categories);
            });
        }

        if ($request->sort) {
            $builder = QuestionService::sortBuilder($builder, $request);
        }

        //cache
        return $builder->paginate(10)
            ->withQueryString();
    }

    private static function sortBuilder(Builder $builder, Request $request)
    {
        if ($request->sort === 'popular') {
            $builder = Question::getPopular($builder);
        } else {
            try {
                [$sortBy, $order] = explode('_', $request->sort);
                $builder->orderBy($sortBy, $order);
            } catch (Throwable $err) {
                $builder->orderBy('id', 'desc');
            }
        }

        return $builder;
    }

    public function getPopularForWeek()
    {
        $collection = $this->getRawDataForPopular('1 WEEK');
    }

    public static function getPopular()
    {
        $collection = self::getRawDataForPopular('1 YEAR'); //todo

        if ($collection->isEmpty())
            return new Collection([]);

        $questionIds = array_column($collection->toArray(), 'id');

        $data = Question::active()
            ->whereIn('id', $questionIds)
            ->orderBy(DB::raw("FIELD(id, " . implode(',', $questionIds) . ")"))
            ->get();

//        dump('sec',microtime(true) - $time);
        return $data;
    }

    private static function getRawDataForPopular(string $interval = '1 DAY')
    {
        if ($interval === '1 DAY') {
            // cache 3h
        } else {
            // cache 12h
        }

        $statistics = DB::table('question_statistics')
            ->select(['question_id', 'views']);

        $votes = DB::table('question_user_votes')
            ->groupBy('question_id')
            ->select(['question_id', DB::raw('SUM(vote) as total_votes')]);

        return DB::table('questions as q')
            ->select([
                'q.id',
                DB::raw('(COALESCE(statistics.views, 0) * 0.2 + COALESCE(votes.total_votes, 0)) as popularity'),
            ])
            ->leftJoinSub($statistics, 'statistics', 'statistics.question_id', '=', 'q.id')
            ->leftJoinSub($votes, 'votes', 'votes.question_id', '=', 'q.id')
            ->where('q.created_at', '>', DB::raw('NOW() - INTERVAL ' . $interval))
            ->orderBy('popularity', 'desc')
            ->get();
    }
}