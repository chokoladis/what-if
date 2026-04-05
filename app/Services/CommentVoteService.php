<?php

namespace App\Services;

use App\Models\CommentVotes;
use Illuminate\Support\Facades\Auth;

class CommentVoteService
{
    /**
     * @param array<string, int> $data
     * @return void
     */
    public function vote(array $data): void
    {
        /** @var CommentVotes $commentVote */
        $commentVote = CommentVotes::firstOrCreate([
            'comment_id' => $data['entity_id'],
            'user_id' => Auth::id()
        ], [
            'comment_id' => $data['entity_id'],
            'user_id' => Auth::id(),
            'vote' => $data['vote'],
        ]);

        if (!$commentVote->wasRecentlyCreated) {
            if ($commentVote->vote === $data['vote']) {
                $commentVote->delete();
            } else {
                $commentVote->update(['vote' => $data['vote']]);
            }
        }
    }
}