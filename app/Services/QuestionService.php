<?php

namespace App\Services;

use App\DTO\Errors\CommonError;
use App\DTO\Errors\ValidationError;
use App\Exceptions\FileSaveException;
use App\Http\Requests\Question\IndexRequest;
use App\Http\Requests\Question\StoreRequest;
use App\Models\Category;
use App\Models\Question;
use App\Models\QuestionComments;
use App\Models\QuestionTags;
use App\Models\QuestionVotes;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class QuestionService
{
    const POPULAR_VIEW_RATIO = 0.2;
    const DEFAULT_LIMIT = 10;

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

        DB::beginTransaction();

        [$data, $error] = $this->prepareStoreData($request);

        if (!$data) {
            DB::rollBack();

            return [false, $error];
        }

        if (isset($data['tags'])){
            $tags = $data['tags'];
            unset($data['tags']);
        }

        $res = Question::create($data);

        if (empty($res)) {
            DB::rollBack();
            return [false, new CommonError('Не удалось создать вопрос')];
        } elseif (!empty($res) && !empty($tags)){
            $tags = Tag::query()->whereIn('name', $tags)->get('id');

            foreach ($tags as $tag) {
                $createdTag = QuestionTags::create([
                    'question_id' => $res->id,
                    'tag_id' => $tag->id
                ]);
                if (!$createdTag) {
                    DB::rollBack();
                    return [false, new CommonError('Не удалось задать теги для вопроса')];
                }
            }
        }

        DB::commit();

        return [$res, null];
    }

    private function prepareStoreData(StoreRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();
        $data['active'] = $request->user()->can('isAdmin', auth()->user());

        try {
            if ($request->has('img') && $img = $request->file('img')) {
                if ($img->isValid()) {
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
        } catch (FileSaveException $e) {
            return [null, new CommonError($e->getMessage(), $e->getCode())];
        } catch (\Throwable $th) {
            Log::error('Prepare data error: ' . $th->getMessage());
            return [null, new CommonError('Ошибка обработки данных: ' . $th->getMessage())];
        }

        return [$data, null];
    }

    public static function paginateWithFilter(IndexRequest $request)
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
            $builder = self::sortBuilder($builder, $request);
        }

        //cache
        return $builder->paginate(perPage: 10, page: $request->page ?? 1)
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
        $collection = $this->getRawDataForPopular(interval: '1 WEEK');
    }

    public static function getPopular(int $limit = self::DEFAULT_LIMIT, string $interval = '1 YEAR')
    {
        $collection = self::getRawDataForPopular(interval: $interval);

        if ($collection->isEmpty())
            return new Collection([]);

        $questionIds = array_column($collection->toArray(), 'id');

        return Cache::remember(serialize('question_popular_'.$limit.'_'.$interval), 3600*3, function () use ($questionIds, $limit) {
            return Question::active()
                ->whereIn('id', $questionIds)
                ->limit($limit)
                ->orderBy(DB::raw("FIELD(id, " . implode(',', $questionIds) . ")"))
                ->get();
        });
    }

    private static function getRawDataForPopular(int $limit = self::DEFAULT_LIMIT, string $interval = '1 DAY')
    {
        if ($interval === '1 DAY') {
            $cacheTtl = 3600 * 3;
        } else {
            $cacheTtl = 3600 * 12;
        }
        $key = serialize('question_raw_popular_'.$limit.'_'.$interval);

        return Cache::remember($key, $cacheTtl, function () use ($limit, $interval) {
            $statistics = DB::table('question_statistics')
                ->select(['question_id', 'views']);

            $votes = DB::table('question_votes')
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
                ->limit($limit)
                ->orderBy('popularity', 'desc')
                ->get();
        });
    }

    static function getVotes(int $id)
    {
        return Cache::remember('question_votes_'.$id, 3600*3, function () use ($id) {
            $tableName = (new QuestionVotes)->getTable();
            return QuestionVotes::query()
                ->select(
                    DB::raw('(SELECT COUNT(vote) from `'.$tableName.'` WHERE vote = 1 && `question_id` =' . $id . ') as likes'),
                    DB::raw('(SELECT COUNT(vote) from `'.$tableName.'` WHERE vote = -1 && `question_id` =' . $id . ') as dislikes')
                )
                ->first();
        });
    }
}