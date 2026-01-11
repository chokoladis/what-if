<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class QuestionVotes extends Model
{
    use HasFactory;

    public $guarded = [];

    public function getTable()
    {
        return 'question_user_votes';
    }

    public static function getByQuestionId(int $id)
    {
//        cache , to service
        return self::query()
            ->select(
                DB::raw('(SELECT COUNT(vote) from `question_user_votes` WHERE vote = 1 && `question_id` =' . $id . ') as likes'),
                DB::raw('(SELECT COUNT(vote) from `question_user_votes` WHERE vote = -1 && `question_id` =' . $id . ') as dislikes')
            )
            ->first();
    }

    public static function getById(int $id)
    {
//        cache , to service
        return self::query()
            ->select(
                DB::raw('(SELECT COUNT(vote) from `question_user_votes` WHERE vote = 1 && `question_id` =' . $id . ') as likes'),
                DB::raw('(SELECT COUNT(vote) from `question_user_votes` WHERE vote = -1 && `question_id` =' . $id . ') as dislikes')
            )
            ->first();
    }

    public static function getByQuestionIdForUser(int $id)
    {
        return self::where('question_id', $id)->where('user_id', auth()->id())->first('vote');
    }
}
