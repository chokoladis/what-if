<?php

namespace App\Http\Controllers;

use App\Http\Requests\VoteStoreRequest;
use App\Services\CommentVoteService;
use Illuminate\Http\Response;

class CommentVoteController extends Controller
{
    public CommentVoteService $voteService;

    public function __construct()
    {
        $this->voteService = new CommentVoteService;
    }

    public function vote(VoteStoreRequest $request): Response
    {
        $this->voteService->vote($request->validated());

        return responseJson();
    }
}
