<?php

namespace App\Http\Controllers;

use App\Http\Requests\VoteStoreRequest;
use App\Models\CommentVotes;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class CommentVotesController extends Controller
{
    public function vote(VoteStoreRequest $request): Response
    {
//        todo rework
        $data = $request->validated();
        $comment_id = $data['entity_id'];
        $vote = $data['vote'];
        $userId = Auth::id();

        $commentVote = CommentVotes::query()
            ->where('comment_id', $comment_id)
            ->where('user_id', $userId)
            ->where('vote', $vote)
            ->first();

        if ($commentVote) {
            $commentVote->delete();
        } else {
            CommentVotes::updateOrCreate([
                'comment_id' => $comment_id, 'user_id' => $userId
            ], [
                'vote' => $vote
            ]);
        }

        return responseJson();
    }
}
