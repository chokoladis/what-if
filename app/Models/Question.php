<?php

namespace App\Models;

use App\DTO\Indexing\CommentDTO;
use App\DTO\Indexing\UserDTO;
use App\Models\Errors\CommonError;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravel\Scout\Searchable;

class Question extends BaseModel
{
//    use Searchable;

    public $guarded = [];

    public static function getElement(string $code)
    {
        // use cache 
        if ($question = Question::where('code', $code)->where('active', true)->first()) {
            return [$question, null];
        } else {
            return [false, new CommonError(__('questions.alerts.not_available'))];
        }
    }

    public static function getActive()
    {
        //cache
        return Question::query()->where('active', true)->get();
    }

    public static function getTopPopular()
    {
        //cache
        $query = Question::query()
            ->where('active', true)
            ->with(['statistics' => function ($q) {
                $q->orderBy('views', 'desc');
            }, 'statistics'])
            ->with(['votes' => function ($q) {
                $q->sum('votes');
            }, 'user_votes'])
            ->limit(10)
            ->get();

        return $query;
    }

    public function getCurrentUserComment()
    {

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

    public function getPopularComment()
    {
        if (!$this->comments->isEmpty()) {

            $query = DB::table('comments')
                ->join('questions', 'questions.id', '=', 'comments.question_id')
                ->join('comment_votes', 'comments.id', '=', 'comment_votes.comment_id')
                ->where('comments.question_id', $this->id)
                ->where('comments.active', true)
                ->select('comment_votes.vote', 'comment_votes.comment_id')
                ->get();

            $comments = [];

            if ($query->isNotEmpty()) {
//                dump($query);
                foreach ($query as $comment) {

                    // todo check
                    if (!isset($comments[$comment->comment_id]))
                        $comments[$comment->comment_id] = 0;

                    $comments[$comment->comment_id] = $comments[$comment->comment_id] + $comment->vote;
                }

                $popularCommentId = array_search(max($comments), $comments);

                return Comment::query()->find($popularCommentId);
            }

            return false;
        }

        return false;
    }

    public function category(): HasOne
    {
        return $this->HasOne(Category::class, 'id', 'category_id');
    }

    //    todo
    public function comments() : HasMany
    {
        return $this->hasMany(Comment::class, 'question_id', 'id')
            ->where('active', true);
    }

    public function file(): HasOne
    {
        return $this->hasOne(File::class, 'id', 'file_id');
    }

    public function statistics(): HasOne
    {
        return $this->hasOne(QuestionStatistics::class, 'question_id', 'id');
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function votes(): hasMany
    {
        return $this->hasMany(QuestionVotes::class, 'id', 'question_id');
    }

    public function right_comment(): hasOne
    {
//        todo rework
        return $this->HasOne(Comment::class, 'id', 'right_comment_id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'question_tags', 'question_id', 'tag_id');
    }


    public static function boot()
    {

        parent::boot();

        /**
         * Write code on Method
         *
         * @return response()
         */
        static::creating(function ($item) {
            $item->code = Str::slug(Str::lower($item->title), '-');
        });

        static::created(function ($item) {
            // File::find($item->file_id)->update(['question_id' => $item->id]);
            QuestionStatistics::create([
                'question_id' => $item->id
            ]);
        });

        // updated to active = true // send sms/mail message about it
    }

    public function scopeSearch(\Illuminate\Database\Eloquent\Builder $query, string $title)
    {
        return $query->where('title', 'LIKE', '%' . $title . '%')
            ->orWhere('code', 'LIKE', '%' . $title . '%');
    }

    public function toSearchableArray()
    {
        $obj = [
            'title' => $this->title,
            'code' => $this->code,
            'category_list' => $this->getCategoryIds(),
            'category_id' => $this->category_id,
            'file' => $this->file,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];

        $rightComment = $this->right_comment;
        if ($rightComment) {
            $obj['right_comment'] = New CommentDTO(
                $rightComment->id,
                $rightComment->text,
                New UserDTO($rightComment->user)
            );
        }

        $popularComment = $this->getPopularComment();
        if ($popularComment) {
            $obj['popular_comment'] = new CommentDTO(
                $popularComment->id,
                $popularComment->text,
                new UserDTO($popularComment->user)
            );
        }

        return $obj;
    }

    public function shouldBeSearchable()
    {
        return $this->active;
    }

    public function getCategoryIds()
    {
        $categoryId = $this->category_id;

        $arIds = [$categoryId];

        while (true) {
            $category = Category::query()
                ->where('active', true)
                ->where('id', $categoryId)
                ->whereNotNull('parent_id')->first();

            if ($category && $category->parent_id) {
                $categoryId = $category->parent_id;
                $arIds[] = $categoryId;
            } else {
                break;
            }
        }

        return $arIds;
    }

    public static function getPopular(Builder $builder)
    {
        // todo with likes
        return $builder->join('question_statistics', 'questions.id', '=', 'question_id')
            ->orderBy('views', 'desc');
    }

    public static function scopeActive()
    {
        return Question::query()->where('active', true);
    }

    public function getShortTitle()
    {
        return mb_strlen($this->title) > 15 ? mb_substr($this->title,0, 15).'...' : $this->title;
    }
}
