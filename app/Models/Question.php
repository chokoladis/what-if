<?php

namespace App\Models;

use App\Exceptions\Models\QuestionNotFoundException;
use App\Interfaces\Models\SearchableInterface;
use App\Models\Errors\CommonError;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Question extends BaseModel
{
    use HasFactory;

    public $guarded = [];

    public static function getElement(string $code){
        // use cache 
        if ($question = Question::where('code', $code)->where('active', true)->first()){
            return [$question, null];
        } else {
            return [false, new CommonError(__('questions.alerts.not_available'))];
        }
    }

    public static function getActive(){
        //cache
        return Question::query()->where('active', true)->get();
    }
    
    public static function getTopPopular(){
        //cache
        $query = Question::query()
            ->where('active', true)
            ->with(['statistics' => function($q) {
                    $q->orderBy('views', 'desc');
                }, 'statistics'])
            ->with(['user_votes' => function($q) {
                $q->sum('status');
            }, 'user_votes'])
            ->limit(10)
            ->get();

        return $query;
    }

    public function getCurrentUserComment(){

        $userId = auth()->id();

        if (!$userId)
            return false;

        $res = QuestionComments::query()
            ->where('question_id', $this->id)
            ->join('comments', 'comments.id', '=', 'comment_id')
            ->where('comments.user_id', auth()->id())
            ->first();
        
        return $res;
    }

    public function getPopularComment(){
        
        $result = false;

        if ($this->question_comment){

            // todo deficlt sql
            $query = QuestionComments::where('question_id',$this->id)
                ->join('comment_user_votes as statuses','question_comments.comment_id','=','statuses.comment_id')
                ->select(['statuses.status','statuses.comment_id'])
                ->get();

//            $query = QuestionComments::query()
//                ->where('question_id',$this->id)
////                ->where('question_comments.question_id', $this->id)
//                ->join('comment_user_votes as statuses','question_comments.comment_id','=','statuses.comment_id')
//                ->select(['question_comments.id', 'question_comments.comment_id', 'statuses.status','statuses.comment_id'])
//                // ->groupBy('statuses.comment_id')
//                ->get();

            $comments = [];

            if ($query->isNotEmpty()){
                foreach ($query as $comment) {

                    if (!isset($comments[$comment->comment_id]))
                        $comments[$comment->comment_id] = 0;

                    $plus = $comment->status === 'like' ? 1 : -1;

                    $comments[$comment->comment_id] = $comments[$comment->comment_id] + $plus;
                }

                $popularCommentId = array_search(max($comments), $comments);

                $popularComment = Comment::query()->where('id', $popularCommentId)->first();

                return $popularComment;
            }
            
            return false;
        }

        return false;

    }

    public function category() : HasOne {
        return $this->HasOne(Category::class, 'id', 'category_id');
    }

    public function question_comment() : HasMany {
        return $this->HasMany(QuestionComments::class, 'question_id', 'id')
            ->join('comments','question_comments.comment_id','=','comments.id')
            ->where('comments.active', true);
    }

    public function file() : HasOne {
        return $this->hasOne(File::class, 'id', 'file_id');
    }

    public function statistics() : HasOne {
        return $this->hasOne(QuestionStatistics::class, 'question_id', 'id');
    }

    public function user() : HasOne {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function user_votes() : hasMany {
        return $this->hasMany(QuestionUserStatus::class, 'id', 'question_id');
    }

    public function right_comment() : hasOne
    {
        return  $this->HasOne(Comment::class, 'id', 'right_comment_id');
    }


    public static function boot() {

        parent::boot();

        /**
         * Write code on Method
         *
         * @return response()
         */
        static::creating(function($item) {
            $item->code = Str::slug(Str::lower($item->title),'-');
        });

        static::created(function($item) {
            // File::find($item->file_id)->update(['question_id' => $item->id]);
            QuestionStatistics::create([
                'question_id' => $item->id
            ]);
        });

//        updated to active = true // send sms/mail message about it

    }

//    public function prepareSearchData($request)
//    {
//        $data = $request->validated();
//
//        if (isset($data['q'])){
//            $filter = [
//                'title' => ['title', 'LIKE', '%' . $data['q'] . '%'],
//            ];
//        }
//    }
}
