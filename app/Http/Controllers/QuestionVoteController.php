<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserVoteStore;
use App\Models\Question;
use App\Models\QuestionVotes;

class QuestionVoteController extends Controller
{
    public function set(UserVoteStore $request)
    {
        $strPath = $request->server('REQUEST_URI');
        $arPath = explode('/', $strPath);
        $questionCode = $arPath[count($arPath) - 2];

        if ($question = Question::where('code', $questionCode)->first()){
            $data = $request->validated();
            $data['user_id'] = auth()->id();

            $model = QuestionVotes::query()
                ->where('question_id', $question->id)
                ->where('user_id', auth()->id())
                ->first();
            if (!$model) {
                $status = QuestionVotes::create([
                    'question_id' => $question->id, 'user_id' => $data['user_id'], 'vote' => $data['vote']
                ]);
            } else {
                if ($model->vote === $data['vote']){
                    $status = $model->delete();
                } else {
                    $status = $model->update([
                        'vote' => $data['vote']
                    ]);
                }
            }
        } else {
            $status = false;
        }

        return response()->json(['status' => $status]);
    }
}
