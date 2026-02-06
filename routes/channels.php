<?php

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Support\Facades\Broadcast;


//Broadcast::private('question.vote'); //${voteId}
//, function (\App\Models\User $user, int $voteId) {
//    return $user->id === \App\Models\QuestionVotes::findOrFail($voteId)->user_id;
//});