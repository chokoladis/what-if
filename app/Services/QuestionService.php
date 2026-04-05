<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\FileSaveException;
use App\Http\Requests\Question\IndexRequest;
use App\Http\Requests\Question\StoreRequest;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Question;
use App\Models\QuestionComments;
use App\Models\QuestionTags;
use App\Models\QuestionVotes;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

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

    private static string $model = Question::class;

    /**
     * @param array<string, mixed> $filter
     * @param array<int, string> $sort
     * @param int $limit
     * @return LengthAwarePaginator|false
     */
    public static function getList(
        array $filter = [],
        array $sort = [],
        int   $limit = 10
    ): LengthAwarePaginator|false
    {
        if (empty($filter)) {
            return false;
        }

        $key = serialize($filter) . '_' . serialize($sort) . '_' . $limit;

        $res = Cache::remember('question_list_' . $key, 36000, function () use ($filter, $sort, $limit) {
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
        });

        return $res;
    }

    public static function paginateWithFilter(IndexRequest $request): AbstractPaginator
    {
        $builder = Question::active();

        if ($request->tags) {
            $builder->whereHas('tags', function ($query) use ($request) {
                return $query->whereIn('tags.name', $request->tags);
            });
        }

        if ($request->filled('resolved')) {
            if (filter_var($request->resolved, FILTER_VALIDATE_BOOLEAN)) {
                $builder->whereHas('right_comment');
            } else {
                $builder->whereDoesntHave('right_comment');
            }
        }

        if ($request->categories) {
            $builder->whereHas('category', function ($query) use ($request) {
                return $query->whereIn('categories.title', $request->categories);
            });
        }

        if ($request->sort) {
            $builder = self::sortBuilder($builder, $request);
        }

//        'right_comment.user'
        return $builder
            ->with(['file', 'category', 'tags', 'user', 'statistics', 'votes', 'right_comment'])
            ->paginate(perPage: 10, page: $request->page ?? 1)
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

    public static function getPopular(int $limit = self::DEFAULT_LIMIT, string $interval = '1 YEAR')
    {
//        for rework
        $collection = self::getRawDataForPopular(interval: $interval);

        if ($collection->isEmpty())
            return new Collection([]);

        $questionIds = array_column($collection->toArray(), 'id');

        return Cache::remember(serialize('question_popular_' . $limit . '_' . $interval), 3600 * 3, function () use ($questionIds, $limit) {
            return Question::active()
//                ->selectRaw('id, code, title, file_id, user_id,
//                (   SELECT comments.id FROM comments
//                    WHERE comments.question_id = questions.id
//                        AND comments.is_answer = true
//                        AND comments.active = true) AS right_comment_id')
                ->with(['file', 'user', 'right_comment', 'right_comment.user'])
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
        $key = serialize('question_raw_popular_' . $limit . '_' . $interval);

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

    static function getVotes(int $id): ?QuestionVotes
    {
//        for rework or delete
        return Cache::remember('question_votes_' . $id, 3600 * 3, function () use ($id) {
            $tableName = (new QuestionVotes)->getTable();
            return QuestionVotes::query()
                ->select(
                    DB::raw('(SELECT COUNT(vote) from `' . $tableName . '` WHERE vote = 1 && `question_id` =' . $id . ') as likes'),
                    DB::raw('(SELECT COUNT(vote) from `' . $tableName . '` WHERE vote = -1 && `question_id` =' . $id . ') as dislikes')
                )
                ->first();
        });
    }

    public function setRightComment($data): bool
    {
//        todo add index active
        /** @var ?Comment $comment */
        $comment = Comment::active()->where('id', $data['comment_id'])->with(['question', 'question.user'])->first();
        if ($comment->question->user->id === Auth::id()) {
            return $comment->update(['is_answer' => true]);
        }

        return false;
    }

    public function store(StoreRequest $request): mixed
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

        if (isset($data['tags'])) {
            $tags = $data['tags'];
            unset($data['tags']);
        }

        $res = Question::create($data);

        if (empty($res)) {
            DB::rollBack();
            return [false, 'Не удалось создать вопрос'];
        } elseif (!empty($res) && !empty($tags)) {
            $tags = Tag::query()->whereIn('name', $tags)->get('id');

            foreach ($tags as $tag) {
                $createdTag = QuestionTags::create([
                    'question_id' => $res->id,
                    'tag_id' => $tag->id
                ]);
                if (!$createdTag) {
                    DB::rollBack();
                    return [false, 'Не удалось задать теги для вопроса'];
                }
            }
        }

        DB::commit();

        return [$res, null];
    }

    /**
     * @param StoreRequest $request
     * @return array{array|false, ?string}
     * @throws ModelNotFoundException
     */
    private function prepareStoreData(StoreRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = Auth::id();
        $data['active'] = $request->user()->can('isAdmin', Auth::user());

        try {
            if ($request->has('img') && $img = $request->file('img')) {
                if ($img->isValid()) {
                    $res = FileService::save($img, 'questions');
                    $data['file_id'] = $res['id'];
                    unset($data['img']);
                } else {
                    // todo translate
                    return [false, 'Файл не валиден'];
                }
            }

            if (isset($data['category'])) {
                if ($category = Category::getByCode($data['category'])) {
                    $data['category_id'] = $category->id;
                }
                unset($data['category']);
            }
        } catch (FileSaveException $e) {
            return [false, $e->getMessage()];
        } catch (Throwable $th) {
            Log::error('Prepare data error: ' . $th->getMessage(), [$th]);
            return [false, 'Ошибка обработки данных'];
        }

        return [$data, null];
    }

    public function getWithFullData(string $code): ?Question
    {
//        todo with join
        return Cache::remember('question_full_data_' . $code, 86400, function () use ($code) {
            return Question::active()
                ->where('code', $code)
                ->with(['file', 'category', 'tags', 'user', 'statistics', 'votes', 'comments', 'right_comment'])
                ->first();
        });
    }
}