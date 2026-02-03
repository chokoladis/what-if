<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('question.vote.{voteId}', function (\App\Models\User $user, int $voteId) {
    return $user->id === \App\Models\QuestionVotes::findOrFail($voteId)->user_id;
});