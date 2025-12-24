<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserVoteStore;
use App\Models\QuestionUserVotes;

class QuestionUserVoteController extends Controller
{
    public function set(UserVoteStore $request)
    {
//        dd($request);

        $data = $request->validated();
        $data['user_id'] = auth()->id();

        $status = QuestionUserVotes::updateOrCreate(
            ['question_id' => $data['entity_id'], 'user_id' => $data['user_id']],
            $data
        );

        return response()->json(['status' => $status]);
    }
}
