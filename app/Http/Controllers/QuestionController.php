<?php

namespace App\Http\Controllers;

use App\Events\ViewEvent;
use App\Http\Requests\Question\StoreRequest;
use App\Models\Category;
use App\Models\Question;
use App\Models\QuestionUserStatus;
use App\Services\FileService;

class QuestionController extends Controller
{

    public function index(){
        $questions = Question::getActive();
        return view('questions.index', compact('questions'));
    }

    public function add(){

        $categories = Category::getDaughtersCategories();

        return view('questions.add', compact('categories'));
    }

    public function store(StoreRequest $request){

        $data = $request->validated();
        $data['user_id'] = auth()->id();//user()->id; // заглушка
        $data['active'] = $request->user()->can('isAdmin', auth()->user());

        try {
            if ($request->hasFile('img')){
                $img = $request->file('img');
                if ($img->isValid()){                    
                    $res = FileService::save($img,'questions');
                    $data['file_id'] = $res['id'];
                } else {
                    // return JsonResponse(['error' => 'Не валидный файл']);
                }
            }
            unset($data['img']);
            
            $question = Question::firstOrCreate([
                'title' => $data['title']
            ],$data);

            if ($question->wasRecentlyCreated){
                return redirect()->route('questions.index')->with('message', 'Вопрос сохранен и будет опубликован позже');
            } else {
                return redirect()->back()->with('message', 'Такой вопрос уже задавали.');
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
                $reply = $comment->reply;
                if ($reply->isNotEmpty()){
                    $arComments[$comment->id]['comment'] = $comment;
                    $arComments[$comment->id]['items'] = $reply;
                }
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
}
