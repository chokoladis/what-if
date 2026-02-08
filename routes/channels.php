<?php

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Support\Facades\Broadcast;


Broadcast::channel('comment.vote.{id}', function (\App\Models\User $user, $id) {
    return (int) $user->id === (int) $id;
});

//, function (\App\Models\User $user, int $voteId) {
//    return $user->id === \App\Models\QuestionVotes::findOrFail($voteId)->user_id;
//});