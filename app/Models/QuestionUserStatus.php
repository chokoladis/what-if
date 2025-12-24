<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class QuestionUserVotes extends Model
{
    use HasFactory;

    public $guarded = [];

    public static function getByQuestionId(int $id)
    {
//        cache , to service
        return self::query()
            ->select(
                DB::raw('(SELECT COUNT(status) from `question_votes` WHERE status = 1 && `question_id` ='.$id.') as likes'),
                DB::raw('(SELECT COUNT(status) from `question_votes` WHERE status = 0 && `question_id` ='.$id.') dislikes')
            )
            ->first();
    }

    public static function getByQuestionIdForUser(int $id)
    {
        return self::where('question_id', $id)->where('user_id', auth()->id())->first('status');
    }
}
