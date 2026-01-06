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

            $status = QuestionVotes::updateOrCreate(
                ['question_id' => $question->id, 'user_id' => $data['user_id']],
                ['vote' => $data['vote']]
            );
        } else {
            $status = false;
        }

        return response()->json(['status' => $status]);
    }
}
