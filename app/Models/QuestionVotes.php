<?php

namespace App\Models;

use App\Enums\Vote;
use App\Notifications\Question\VoteNotification;
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

    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
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

            if (Vote::from($item->vote) === Vote::LIKE) {

                //        todo middleware or base service / magic method ?
                if (strtolower(config('notification.status')) === 'off') {
                    return;
                }

                $notification = new VoteNotification($item->user, $item->question);
                if (!VoteNotification::isExists($notification)) {
                    $item->question->user->notify($notification);
                }
            }

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
