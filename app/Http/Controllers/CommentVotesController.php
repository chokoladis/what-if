<?php

namespace App\Http\Controllers;

use App\Http\Requests\VoteStoreRequest;
use App\Models\CommentVotes;

class CommentVotesController extends Controller
{
    public function vote(VoteStoreRequest $request)
    {
        $data = $request->validated();
        $comment_id = $data['entity_id'];
        $vote = $data['vote'];

        $commentVote = CommentVotes::query()
            ->where('comment_id', $comment_id)
            ->where('user_id', auth()->id())
            ->where('vote', $vote)
            ->first();

        if ($commentVote) {
            $commentVote->delete();

            return true;
        }

        CommentVotes::updateOrCreate([
            'comment_id' => $comment_id, 'user_id' => auth()->id()
        ], [
            'vote' => $vote
        ]);

        return true;
    }
}
