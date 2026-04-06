<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Question;
use App\Models\QuestionVotes;
use Illuminate\Support\Facades\Auth;

class QuestionVoteService
{
    /**
     * @param string $code
     * @param array<string, int> $data
     * @return bool
     */
    public function vote(string $code, array $data): bool
    {
        if ($question = Question::getByCode($code)) {
            $data['user_id'] = Auth::id();

            /** @var QuestionVotes $questionVote */
            $questionVote = QuestionVotes::firstOrCreate([
                'question_id' => $question->id,
                'user_id' => $data['user_id']
            ], [
                'question_id' => $question->id, 'user_id' => $data['user_id'], 'vote' => $data['vote']
            ]);

            if ($questionVote->wasRecentlyCreated) {
                $status = true;
            } else {
                if ($questionVote->vote === $data['vote']) {
                    $status = $questionVote->delete();
                } else {
                    $status = $questionVote->update([
                        'vote' => $data['vote']
                    ]);
                }
            }
        }

        return $status ?? false;
    }
}