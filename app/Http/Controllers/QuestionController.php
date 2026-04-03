<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Events\ViewEvent;
use App\Http\Requests\Question\IndexRequest;
use App\Http\Requests\Question\RightCommentStoreRequest;
use App\Http\Requests\Question\StoreRequest;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Question;
use App\Models\QuestionVotes;
use App\Models\Tag;
use App\Repositories\CategoryRepository;
use App\Repositories\TagRepository;
use App\Services\QuestionService;
use App\Services\UserService;
use Error;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;

class QuestionController extends Controller
{
    private QuestionService $questionService;

//    private QuestionIndexService $questionIndexService;
    private TagRepository $tagRepository;
    private CategoryRepository $categoryRepository;

    function __construct()
    {
        $this->questionService = new QuestionService();
//        $this->questionIndexService = new QuestionIndexService();
        $this->tagRepository = new TagRepository();
        $this->categoryRepository = new CategoryRepository(Category::class, true);
    }


    public static function findByUrl(string $url) : ?Question
    {
        $urlExplode = explode('/', $url);
        $questionCode = $urlExplode[count($urlExplode) - 1];

        $question = Question::query()->where('code', $questionCode)->first();
        return $question;
    }

    /**
     * @param IndexRequest $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\View\View|object
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function index(IndexRequest $request)
    {
//        $data = $this->questionIndexService->getIndexPageData($request);
        $tags = $this->tagRepository->getAll();
        $categories = $this->categoryRepository->getActive();

        $key = serialize('questions_' . json_encode($request->validated()));

        // todo rework to search index
        $questions = Cache::get($key);
        if (!$questions) {
            $questionPaginator = QuestionService::paginateWithFilter($request);
            if ($questionPaginator->count()) {
                Cache::set($key, $questionPaginator, 3600);
                $questions = $questionPaginator;
            } else {
                Cache::set($key, null, 3600);
            }
        }

        return view('questions.index', compact('questions', 'tags', 'categories'));
    }

    public function add() : View
    {
        $categories = Category::getDaughtersCategories();
        //cache
        $tags = Tag::all();

        return view('questions.add', compact('categories', 'tags'));
    }

    public function store(StoreRequest $request) : RedirectResponse
    {
        [$question, $error] = $this->questionService->store($request);

        if (!$question) {
            return redirect()->back()->withErrors('error', $error)->withInput();
        }

        if ($question->wasRecentlyCreated) {
            return redirect()->route('questions.index')->with('message', __('questions.alerts.store'));
        } else {
            return redirect()->back()->with('message', __('questions.alerts.already_exists'));
        }
    }

    /**
     * @param string $code
     * @return \Illuminate\Contracts\View\View|\Illuminate\View\View|object
     */
    public function detail(string $code)
    {
        $question = Question::getByCode($code);

        if (!$question) {
//            or to session ?
            return view('questions.detail', ['error' => __('questions.alerts.not_available')]);
        }

        Event(new ViewEvent($question));

        $arVotes = QuestionService::getVotes($question['id']);
        //        todo
        $questionCurrentUserVote = QuestionVotes::getByQuestionIdForUser($question['id']);

        $arComments = [];
//            mb use algoritm
        /* @var Comment $comment */
        foreach ($question->comments as $comment) {

            if ($comment->isReply()) {
                continue;
            }

            $countChilds = $comment->getCountChilds($comment->replies);

            $arComments[$comment->id]['comment'] = $comment;
            $arComments[$comment->id]['count_childs'] = $countChilds;
        }

        $isNeedShowFullTitle = false;

        if (mb_strlen($question->title) > 70) {
            $title = mb_strcut($question->title, 0, 70) . '...';
            $isNeedShowFullTitle = true;
        } else {
            $title = $question->title;
        }

        return view('questions.detail',
            compact('question', 'arVotes', 'questionCurrentUserVote', 'arComments', 'title', 'isNeedShowFullTitle')
        );
    }

    /**
     * @param RightCommentStoreRequest $request
     * @return \Illuminate\Http\Response|object
     */
    public function setRightComment(RightCommentStoreRequest $request)
    {
        $data = $request->validated();

        if ($data['question_id'] < 0 || $data['comment_id'] < 0) {
            return responseJson(false, [
                new Error('Вопрос или комментарий не прошли валидацию', 'question_or_comment_no_valid')
            ]);
        }

        if ($this->questionService->isCommentContains($data) !== null) {
            if ($this->questionService->setRightComment($data)) {
                return responseJson(true);
            }
        }

        return responseJson(false, [
            new Error('Ошибка при задании верного комментария', 'error_in_set_right_comment')
        ]);
    }

    public function recommendations(IndexRequest $request) : View
    {
//        $sidebar = $this->questionIndexService->getSidebarFilterData();
        $tags = Cache::remember('tags_all', 3600, function () {
            return Tag::all();
        });
        $categories = Cache::remember('categories_active', 3600, function () {
            return Category::query()->where('active', 1)->get();
        });

        // todo rework to search index
        $questions = UserService::getRecommendations();

        return view('questions.index', compact('questions', 'tags', 'categories'));
    }
}
