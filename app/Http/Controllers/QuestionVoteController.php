<?php

namespace App\Http\Controllers;

use App\Http\Requests\VoteStoreRequest;
use App\Services\QuestionVoteService;
use Illuminate\Http\JsonResponse;

class QuestionVoteController extends Controller
{
    public QuestionVoteService $voteService;

    public function __construct()
    {
        $this->voteService = new QuestionVoteService;
    }

    public function set(string $question, VoteStoreRequest $request): JsonResponse
    {
        $status = $this->voteService->vote($question, $request->validated());

        return response()->json(['status' => $status]);
    }
}
