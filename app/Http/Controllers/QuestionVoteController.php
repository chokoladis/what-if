<?php

namespace App\Http\Controllers;

use App\Http\Requests\VoteStoreRequest;
use App\Models\Question;
use App\Models\QuestionVotes;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class QuestionVoteController extends Controller
{
    public function set(VoteStoreRequest $request): JsonResponse
    {
        $arPath = explode('/', $request->server('REQUEST_URI'));
        $questionCode = $arPath[count($arPath) - 2];

//        переиспользовать блоки question where code из других областей
        if ($question = Question::where('code', $questionCode)->first()) {
            $data = $request->validated();
            $data['user_id'] = Auth::id();

            /** @var QuestionVotes $questionVote */
            $questionVote = QuestionVotes::firstOrCreate([
                'question_id' => $question->id,
                'user_id' => $data['user_id']
            ],[
                'question_id' => $question->id, 'user_id' => $data['user_id'], 'vote' => $data['vote']
            ]);

            if (!$questionVote->wasRecentlyCreated) {
                $status = true;
            } else {
                if ((int)$questionVote->vote === (int)$data['vote']) {
                    $status = $questionVote->delete();
                } else {
                    $status = $questionVote->update([
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
