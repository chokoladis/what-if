<?php

namespace App\Models;

use App\Events\Broadcast\Question\Vote;
use App\Tools\Option;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class QuestionVotes extends Model
{
    use HasFactory;

    public $guarded = [];

    public function getTable()
    {
        return 'question_votes';
    }

    public static function getByQuestionIdForUser(int $id)
    {
        return self::where('question_id', $id)->where('user_id', auth()->id())->first('vote');
    }

    public static function boot()
    {
            $smartCache = Option::isSmartCacheOn();

            parent::boot();

            /**
             * Write code on Method
             *
             * @return response()
             */
            static::updated(function ($item) use ($smartCache) {
                if ($smartCache){
                    Cache::forget('question_votes_'.$item->question_id);
                }
            });

            static::created(function ($item) use ($smartCache) {

                Vote::dispatch($item);

                if ($smartCache){
                    Cache::forget('question_votes_'.$item->question_id);
                }
            });

            static::deleted(function ($item) use ($smartCache) {
                if ($smartCache){
                    Cache::forget('question_votes_'.$item->question_id);
                }
            });
    }
}
