<?php

namespace App\Http\Controllers;

use App\Http\Requests\VoteStoreRequest;
use App\Models\Question;
use App\Models\QuestionVotes;
use Illuminate\Http\JsonResponse;

class QuestionVoteController extends Controller
{
    public function set(VoteStoreRequest $request) : JsonResponse
    {
        $arPath = explode('/', (string)$request->server('REQUEST_URI'));
        $questionCode = $arPath[count($arPath) - 2];

        if ($question = Question::where('code', $questionCode)->first()) {
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
                if ((int)$model->vote === (int)$data['vote']) {
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
