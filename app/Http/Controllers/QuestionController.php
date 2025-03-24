<?php

namespace App\Http\Controllers;

use App\Events\ViewEvent;
use App\Http\Requests\Question\LoadSubcommentsRequest;
use App\Http\Requests\Question\RightCommentStoreRequest;
use App\Http\Requests\Question\StoreRequest;
use App\Models\Category;
use App\Models\CommentsReply;
use App\Models\Question;
use App\Models\QuestionComments;
use App\Models\QuestionUserStatus;
use App\Services\CaptchaService;
use App\Services\FileService;
use App\Services\QuestionService;
use App\View\Components\CommentReply;
use Error;
use Illuminate\Support\Facades\Request;

class QuestionController extends Controller
{
    const COMMENTS_LIMIT = 10;

    private QuestionService $questionService;

    function __construct()
    {
        $this->questionService = new QuestionService();
    }

    public function index(){
        $questions = Question::getActive();
        return view('questions.index', compact('questions'));
    }

    public function add(){

        $categories = Category::getDaughtersCategories();

        return view('questions.add', compact('categories'));
    }

    public function store(StoreRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();//user()->id; // заглушка
        $data['active'] = $request->user()->can('isAdmin', auth()->user());

        $captcha = new CaptchaService();
        [$success, $error] = $captcha->verify($request->get('h-captcha-response'));

        if (!$success) {    
            return redirect()->back()->with('message', $error);
        }

        try {
            if ($request->hasFile('img')){
                $img = $request->file('img');
                if ($img->isValid()){                    
                    $res = FileService::save($img,'questions');
                    $data['file_id'] = $res['id'];
                } else {
                    return redirect()->back()->with('message', 'File not valid');
                }
            }

            $category = Category::getElement($data['category']);
            $data['category_id'] = $category?->id ?? 0; 

            unset($data['category']);
            unset($data['img']);
            unset($data['h-captcha-response']);
            
            $question = Question::firstOrCreate([
                'title' => $data['title']
            ],$data);

            if ($question->wasRecentlyCreated){
                return redirect()->route('questions.index')->with('message', 'Вопрос будет опубликован после модерации'); //Question saved and will public late
            } else {
                return redirect()->back()->with('message', 'Тако вопрос уже опубликован'); //Like question already public
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function detail($question){

        $arStatuses = $questionUserStatus = $arComments = null;

        [$question, $error] = Question::getElement($question);

        if ($question){

            Event(new ViewEvent($question));

//            service and cache
            $arStatuses = QuestionUserStatus::getByQuestionId($question['id']);
            $questionUserStatus = QuestionUserStatus::getByQuestionIdForUser($question['id']);

//            mb use algoritm
            foreach ($question->question_comment as $questionComment){
                $comment = $questionComment->comment;

                if ($comment->isReply()) {
                    continue;
                }

                $countChilds = $comment->getCountChilds($comment->replies);

                $arComments[$comment->id]['comment'] = $comment;
                $arComments[$comment->id]['count_childs'] = $countChilds;

                // $replies = $comment->getReplies($comment->replies);
                // if (!empty($replies)){    
                //     $arComments[$comment->id]['items'] = $replies;
                // }

            }
        }

        return view('questions.detail', compact('question', 'arStatuses', 'questionUserStatus', 'arComments', 'error'));
    }

    public static function findByUrl(string $url){
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

        if ($this->questionService->isCommentContains($data) !== null){
           if ($this->questionService->setRightComment($data)){
               return responseJson(true);
           }
        }

        return responseJson(false, [
            new \Error('Ошибка при задании верного комментария', 'error_in_set_right_comment')
        ]);
    }

    public function loadSubcomments(LoadSubcommentsRequest $request)
    {
        $data = $request->validated();
        $questionId = $data['question-id'];
        $mainCommentId = $data['comment-id'];
        $offset = $data['offset'] ?? 0;
        
        $isCorrect = QuestionComments::query()
            ->where('question_id', $questionId)
            ->where('comment_id', $mainCommentId)
            ->exists();

        if($isCorrect){

            $question = Question::query()->where('id', $questionId)->first();
            // cache
            $replies = CommentsReply::query()
                ->where('comment_main_id', $questionId)
                ->skip($offset)
                ->take(self::COMMENTS_LIMIT)
                ->get();

            return view('components.comment.subcomments', compact('replies', 'question'));
        } else {
            return false;
        }
    }
}
