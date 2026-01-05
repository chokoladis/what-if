<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionStatistics extends Model
{
    use HasFactory;

    public $guarded = [];

    static function init()
    {
        $questions = Question::getActive();
        foreach ($questions as $question) {
            QuestionStatistics::firstOrCreate([
                'question_id' => $question->id
            ], ['question_id' => $question->id]);
        }
    }

    // при создании
    // QuestionStatistics::firstOrCreate([
    //     'question_id' => $question->id
    // ], [ 'question_id' => $question->id ]);
}
