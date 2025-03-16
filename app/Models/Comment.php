<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;

class Comment extends Model
{
    use HasFactory;
    use SoftDeletes;

    public $guarded = [];

    public function getTable()
    {
        return 'comments';
    }

    public function user() : HasOne {
        return $this->HasOne(User::class, 'id', 'user_id');
    }

    public function user_comment() : HasOne {
        return $this->HasOne(UserComments::class);
    }

    public function isReply() : bool {
        return $this->HasOne(CommentsReply::class, 'comment_reply_id', 'id')->exists();
    }

    public function replies() : HasMany {
        return $this->HasMany(CommentsReply::class, 'comment_main_id', 'id')->orderBy('created_at');
    }

    public function parent() : HasOne {
        return $this->hasOne(CommentsReply::class, 'comment_reply_id', 'id');
    }

    // public function getReplies($replyComments, &$arComments = [])
    // {
    //     $result = [];
    //     $arReplies = [];

    //     dump($replyComments, $arComments);
    //     foreach ($replyComments as $replyComment) {
            
    //         // dump($replyComment);
    //         $comment = $replyComment->comment();
    //         // dump($comment->get());
    //         $comment = $comment->first();
    //         $arComments[$comment->id] = $comment;
    //         $arReplies[] = $comment->replies;
    //     }

    //     if (empty($arReplies)){
    //         return $arComments;
    //     }

    //     return $this->getReplies($arReplies,$arComments);
    // }

    public function getCountChilds($replies, int &$count = 0): int
    {
        if (empty($replies)){
            return $count;
        } elseif(is_array($replies)){
            $replies = CommentsReply::whereIn('comment_main_id', $replies)->orderBy('created_at')->get(['comment_reply_id','id']);

            if ($replies->isEmpty())
                return $count;
        }

        $commentsIds = [];
        foreach( $replies as $reply){
            $commentsIds[] = $reply->comment_reply_id;
            $count++;
        }

        return $this->getCountChilds( $commentsIds, $count);
    }

    

    public function getReplies($replies, &$result = [])
    {
        if (empty($replies)){
            return $result;
        } elseif(is_array($replies)){
            $replies = CommentsReply::whereIn('comment_main_id', $replies)->orderBy('created_at')->get();

            if ($replies->isEmpty())
                return $result;
        }

        $commentsIds = [];
        foreach( $replies as $reply){
            $commentsIds[] = $reply->comment_reply_id;
            $result[] = $reply->reply;
        }
        // limit

        return $this->getReplies( $commentsIds, $result);
    }

    public function getRating() {
        return $this->newQuery()
            ->where('comments.id', $this->id)
            ->join('comment_user_statuses as t_statuses','comments.id','=', 'comment_id')
            ->selectRaw('SUM(t_statuses.status) as rating')
            ->first();
    }

    public static function boot() {

        parent::boot();

        /**
         * Write code on Method
         *
         * @return response()
         */

        static::created(function($item) {
            try {
                $userComment = UserComments::create([
                    'user_id' => auth()->id(),
                    'comment_id' => $item->id
                ]);
    
                if (!$userComment || !$userComment->wasRecentlyCreated){
                    Log::debug(__('Не удалось создать связь комментария - '.$item->id.', пользователя - '. auth()->id));
                }
            } catch (\Throwable $th) {
                Log::debug(__('Исключение при создании связи комментария - '.$item->id.', пользователя - '. auth()->id.' --- '.$th));
            }
        });

    }
}
