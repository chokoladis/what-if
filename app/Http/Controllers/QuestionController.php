<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\DTO\Errors\CommonError;
use App\Events\ViewEvent;
use App\Http\Requests\Question\IndexRequest;
use App\Http\Requests\Question\RightCommentStoreRequest;
use App\Http\Requests\Question\StoreRequest;
use App\Models\Category;
use App\Models\Comment;
use App\Models\QuestionVotes;
use App\Models\Tag;
use App\Repositories\CategoryRepository;
use App\Repositories\TagRepository;
use App\Services\QuestionService;
use App\Services\UserService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Psr\SimpleCache\InvalidArgumentException;

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

    /**
     * @param IndexRequest $request
     * @return Factory|View|Application|\Illuminate\View\View|object
     * @throws InvalidArgumentException
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

    public function add(): View
    {
        $categories = Category::getDaughtersCategories();
        //cache
        $tags = Tag::all();

        return view('questions.add', compact('categories', 'tags'));
    }

    public function store(StoreRequest $request): RedirectResponse
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
     * @return View|RedirectResponse
     */
    public function detail(string $code)
    {
        $question = $this->questionService->getWithFullData($code);

        if (!$question) {
            return view('errors.404', ['error' =>  __('questions.alerts.not_available')]);
        }

        Event(new ViewEvent($question));

        //        todo
        $voteCurrentUser = QuestionVotes::getVoteCurrentUser($question['id']);

        $arComments = [];
        /* @var Comment $comment */
        foreach ($question->comments as $comment) {

//            dump('comment', $comment, $comment->parent);

            if ($comment->parent) {
                continue;
            }

//            todo rework
            $arComments[$comment->id]['comment'] = $comment;
            $arComments[$comment->id]['count_childs'] = $comment->getTotalCountChildren($comment->replies);
        }

        return view('questions.detail',
            compact('question', 'voteCurrentUser', 'arComments')
        );
    }

    /**
     * @param Request $request
     * @return Response|object
     */
    public function setRightComment(Request $request)
    {
        $data = $request->validate([
            'comment_id' => 'required|exists:comments,id',
        ]);
//
//        todo send realizy to service

        if ($this->questionService->setRightComment($data)) {
            return responseJson();
        }

        return responseJson(false, [
            new CommonError('Ошибка при задании верного комментария', 'error_in_set_right_comment')
        ]);
    }

    public function recommendations(IndexRequest $request): View
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
