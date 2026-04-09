<?php

namespace App\Models;

use App\DTO\Indexing\CommentDTO;
use App\DTO\Indexing\UserDTO;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Question extends BaseModel
{
    use HasFactory;

//    use Searchable;

    public $guarded = [];

    public static function getByCode(?string $code): ?Question
    {
        return Cache::remember("question.{$code}", 3600, function () use ($code) {
            return Question::active()->where('code', $code)->first();
        });
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

    public static function boot()
    {

        parent::boot();

        static::creating(function ($item) {
            $item->code = Str::slug(Str::lower($item->title), '-');
        });

        static::created(function ($item) {
            // File::find($item->file_id)->update(['question_id' => $item->id]);
            QuestionStatistics::create([
                'question_id' => $item->id
            ]);
        });

        // todo drop cache detail and list

        // updated to active = true // send sms/mail message about it
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

    //    todo

    public function getCurrentUserComment()
    {
        $userId = auth()->id();

        if (!$userId)
            return false;

        $res = Comment::query()
            ->where('question_id', $this->id)
            ->where('user_id', auth()->id())
            ->first();

        return $res;
    }

    public function category(): HasOne
    {
        return $this->HasOne(Category::class, 'id', 'category_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, 'question_id', 'id')
            ->where('active', true);
    }

    public function statistics(): HasOne
    {
        return $this->hasOne(QuestionStatistics::class, 'question_id', 'id');
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function votes(): HasMany
    {
        return $this->HasMany(QuestionVotes::class, 'question_id', 'id');
    }

    public function right_comment(): HasOne
    {
        return $this->HasOne(Comment::class, 'question_id', 'id')
            ->where('active', true)
            ->where('is_answer', true);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'question_tags', 'question_id', 'tag_id');
    }

    public function scopeSearch(Builder $query, string $title)
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
            $obj['right_comment'] = new CommentDTO(
                $rightComment->id,
                $rightComment->text,
                new UserDTO($rightComment->user)
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

    public function getPopularComment()
    {
        $cacheKey = 'question_popular_comment_' . $this->id;
        $popularCommentId = Cache::remember($cacheKey, 3600, function () {
            $query = DB::table('comments')
                ->join('comment_votes', 'comments.id', '=', 'comment_votes.comment_id')
                ->where('comments.question_id', $this->id)
                ->where('comments.active', true)
                ->select('comment_votes.vote', 'comment_votes.comment_id')
                ->get();

            if ($query->isEmpty()) {
                return 0;
            }

            $comments = [];
            foreach ($query as $comment) {
                if (!isset($comments[$comment->comment_id])) {
                    $comments[$comment->comment_id] = 0;
                }
                $comments[$comment->comment_id] += $comment->vote;
            }

            return (int)array_search(max($comments), $comments, true);
        });

        if (!$popularCommentId) {
            return false;
        }

        return Comment::query()->with('user')->find($popularCommentId);
    }

    public function shouldBeSearchable()
    {
        return $this->active;
    }

    public function getShortTitle(?int $length = 15)
    {
        return mb_strlen($this->title) > $length ? mb_substr($this->title, 0, $length) . '...' : $this->title;
    }
}
