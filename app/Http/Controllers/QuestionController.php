<?php

namespace App\Http\Controllers;

use App\Events\ViewEvent;
use App\Http\Requests\Question\RightCommentStoreRequest;
use App\Http\Requests\Question\StoreRequest;
use App\Models\Category;
use App\Models\Question;
use App\Models\QuestionVotes;
use App\Models\Tag;
use App\Services\QuestionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class QuestionController extends Controller
{
    private QuestionService $questionService;

    function __construct()
    {
        $this->questionService = new QuestionService();
    }

    public function index(Request $request)
    {
        // need cache
        $tags = Tag::all();
        $categories = Category::query()->where('active', 1)->get();

        $key = json_encode($request->all());

        // todo rework to search index
        $questions = Cache::remember('questions_' . $key, 3600, function () use ($request) {
//            $questions = QuestionService::paginateWithFilter($request);
            return QuestionService::paginateWithFilter($request);
        });

        return view('questions.index', compact('questions', 'tags', 'categories'));
    }

    public function add()
    {
        $categories = Category::getDaughtersCategories();
        //cache
        $tags = Tag::all();

        return view('questions.add', compact('categories', 'tags'));
    }

    public function store(StoreRequest $request)
    {
        [$question, $error] = $this->questionService->store($request);

        if (!$question) {
            return redirect()->back()->with('error', $error)->withInput();
        }

        if ($question->wasRecentlyCreated) {
            return redirect()->route('questions.index')->with('message', __('questions.alerts.store'));
        } else {
            return redirect()->back()->with('message', __('questions.alerts.already_exists'));
        }
    }

    public function detail($question)
    {
        [$question, $error] = Question::getElement($question);

        if ($error) {
            return view('questions.detail', compact('error'));
        }

        Event(new ViewEvent($question));

//            service and cache
        $arVotes = QuestionVotes::getByQuestionId($question['id']);
        $questionCurrentUserVote = QuestionVotes::getByQuestionIdForUser($question['id']);

        $arComments = [];
//            mb use algoritm
        foreach ($question->question_comment as $questionComment) {
            $comment = $questionComment->comment;

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

    public static function findByUrl(string $url)
    {
        $urlExplode = explode('/', $url);
        $questionCode = $urlExplode[count($urlExplode) - 1];

        $question = Question::query()->where('code', $questionCode)->first();
        return $question;
    }

    public function setRightComment(RightCommentStoreRequest $request)
    {
        $data = $request->validated();

        if ($data['question_id'] < 0 || $data['comment_id'] < 0) {
            return responseJson(false, [
                new \Error('Вопрос или комментарий не прошли валидацию', 'question_or_comment_no_valid')
            ]);
        }

        if ($this->questionService->isCommentContains($data) !== null) {
            if ($this->questionService->setRightComment($data)) {
                return responseJson(true);
            }
        }

        return responseJson(false, [
            new \Error('Ошибка при задании верного комментария', 'error_in_set_right_comment')
        ]);
    }
}
