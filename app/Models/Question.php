<?php

namespace App\Models;

use App\DTO\Indexing\CommentDTO;
use App\DTO\Indexing\UserDTO;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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

    public static function scopeActive(Builder $query): void
    {
        $query->where('active', true);
    }

    public static function getByCode(?string $code): ?Question
    {
        return Cache::remember("question.{$code}", 3600, function () use ($code) {
            return Question::active()->where('code', $code)->first();
        });
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

    public static function getPopular(Builder $builder) : Builder
    {
        // todo with likes
        $builder->join('question_statistics', 'questions.id', '=', 'question_id')
            ->orderBy('views', 'desc');
        return $builder;
    }

    public function category(): HasOne
    {
        return $this->HasOne(Category::class, 'id', 'category_id');
    }

    /** @return HasMany<Comment, $this> */
    public function comments(): HasMany
    {
        $relation = $this->hasMany(Comment::class, 'question_id', 'id');
        $relation->getQuery()->where('active', true);
        return $relation;
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

    /** @return HasOne<Comment, $this> */
    public function right_comment(): HasOne
    {
        $relation = $this->HasOne(Comment::class, 'question_id', 'id');
        $relation->getQuery()->where('active', true)->where('is_answer', true);

        return $relation;
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'question_tags', 'question_id', 'tag_id');
    }

    public function scopeSearch(Builder $query, string $title): void
    {
        $query->where('title', 'LIKE', '%' . $title . '%')
            ->orWhere('code', 'LIKE', '%' . $title . '%');
    }

    /**
     * @return array<string, mixed>
     */
    public function toSearchableArray(): array
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

        if ($popularComment = $this->getPopularComment()) {
            $obj['popular_comment'] = new CommentDTO(
                $popularComment->id,
                $popularComment->text,
                new UserDTO($popularComment->user)
            );
        }

        return $obj;
    }

    /** @return array<int, int> */
    public function getCategoryIds(): array
    {
        $arIds = [$this->category_id];

        while (true) {
            $category = Category::query()
                ->where('active', true)
                ->where('id', $this->category_id)
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

    public function getPopularComment(): ?Comment
    {
        $cacheKey = 'question_popular_comment_' . $this->id;
        $popularCommentId = Cache::remember($cacheKey, 3600, function () {
            $query = DB::table('comments')
                ->join('comment_votes', 'comments.id', '=', 'comment_votes.comment_id')
                ->where('comments.question_id', $this->id)
                ->where('comments.active', true)
                ->select('comment_votes.vote', 'comment_votes.comment_id')
                ->get();

            if ($query->isEmpty())
                return null;

            $comments = [];
            foreach ($query as $comment) {
                if (!isset($comments[$comment->comment_id])) {
                    $comments[$comment->comment_id] = $comment->vote;
                } else {
                    $comments[$comment->comment_id] += $comment->vote;
                }
            }

            if (empty($comments))
                return null;

            return (int)array_search(max($comments), $comments, true);
        });

        if (!$popularCommentId) {
            return null;
        }

        return Comment::query()
            ->with('user')
            ->firstWhere('id', $popularCommentId);
    }

    public function shouldBeSearchable(): bool
    {
        return $this->active;
    }

    public function getShortTitle(?int $length = 15): string
    {
        return mb_strlen($this->title) > $length ? mb_substr($this->title, 0, $length) . '...' : $this->title;
    }
}
