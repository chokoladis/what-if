<?php

namespace App\Http\Controllers;

use App\DTO\Errors\CommonError;
use App\Http\Requests\Comment\StoreRequest;
use App\Http\Requests\Question\LoadSubcommentsRequest;
use App\Models\Comment;
use App\Models\CommentsReply;
use App\Models\Question;
use App\Models\QuestionComments;

class CommentController extends Controller
{
    public function store(StoreRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();

        $comment_main_id = $data['comment_main_id'];
        unset($data['comment_main_id']);

        $question = QuestionController::findByUrl(url()->previous());
        $comment = Comment::firstOrCreate(['text' => $data['text'], 'user_id' => $data['user_id']], $data);

        if ($comment->wasRecentlyCreated){

            // мб не добавлять везде для подкомментариев
            QuestionComments::create([
                'question_id' => $question->id,
                'comment_id' => $comment->id,
            ]);

            if ($comment_main_id){
                CommentsReply::create([
                    'comment_reply_id' => $comment->id,
                    'comment_main_id' => $comment_main_id,
                ]);
            }

            return redirect()->back()->with('message', __('system.alert.success'));
        } else {
            return redirect()->back()->withErrors('error', new CommonError('Вы уже оставляли такой комментарий'));
        }
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
                ->take(Comment::DEFAULT_LIMIT)
                ->get();

            return view('components.comment.subcomments', compact('replies', 'question'));
        } else {
            return false;
        }
    }
}
