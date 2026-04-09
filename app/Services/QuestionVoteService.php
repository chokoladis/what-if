<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Question;
use App\Models\QuestionVotes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

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

    public static function getVoteCurrentUser(int $id)
    {
//        todo drop in boot create or update
        $userId = Auth::id();
        if ($userId) {
            return Cache::remember('question_' . $id . '_user_' . $userId . '_vote', 86400, function () use ($id, $userId) {
                return QuestionVotes::where('question_id', $id)->where('user_id', $userId)->first('vote');
            });
        }

        return null;
    }

    static function getVotes(int $id): ?QuestionVotes
    {
//        for rework or delete
        return Cache::remember('question_votes_' . $id, 3600 * 3, function () use ($id) {
            $tableName = (new QuestionVotes)->getTable();
            return QuestionVotes::query()
                ->select(
                    DB::raw('(SELECT COUNT(vote) from `' . $tableName . '` WHERE vote = 1 && `question_id` =' . $id . ') as likes'),
                    DB::raw('(SELECT COUNT(vote) from `' . $tableName . '` WHERE vote = -1 && `question_id` =' . $id . ') as dislikes')
                )
                ->first();
        });
    }
}