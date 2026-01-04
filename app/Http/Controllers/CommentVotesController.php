<?php

namespace App\Http\Controllers;

use App\Http\Requests\Comment\UserStatusStoreRequest;
use App\Models\CommentVotes;

class CommentVotesController extends Controller
{
    public function setStatus(UserStatusStoreRequest $request)
    {
        $data = $request->validated();
        $comment_id = intval($data['comment_id']);
        $action = intval($data['action']);

        $commentVote = CommentVotes::query()
            ->where('comment_id', $comment_id)
            ->where('user_id', auth()->id())
            ->where('votes', $action)
            ->first();

        if ($commentVote) {
            $commentVote->delete();

            return true;
        }

        CommentVotes::updateOrCreate([
            'comment_id' => $comment_id, 'user_id' => auth()->id()
        ], [
            'votes' => $action
        ]);

        return true;
    }
}
