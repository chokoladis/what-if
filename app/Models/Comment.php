<?php

namespace App\Models;

use App\Notifications\Comment\CommentNotification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class Comment extends Model
{
    use HasFactory;
    use SoftDeletes;

    const DEFAULT_LIMIT = 10;

    public $guarded = [];

    public function getTable()
    {
        return 'comments';
    }

    public static function scopeActive(): Builder
    {
        return Comment::query()->where('active', true);
    }

    public function scopePopular(): Builder
    {
        return Comment::query()->orderBy('votes_sum_vote', 'desc');
    }

    public function user(): HasOne
    {
        return $this->HasOne(User::class, 'id', 'user_id');
    }

    public function replies(): hasMany
    {
        return $this->hasMany(Comment::class, 'comment_main_id', 'id')
            ->orderBy('created_at');
    }

    public function parent(): HasOne
    {
        return $this->hasOne(Comment::class, 'id', 'comment_main_id');
    }

//        запросить все комментарии не главные по ним сопоставлять по ключам родителей и считать кол-во + включая подкомментарии
//    public function getTotalCountChildren(int $questionId)

    public function votes(): HasMany
    {
        return $this->hasMany(CommentVotes::class, 'comment_id', 'id');
    }

    public function getShortText()
    {
        return mb_strlen($this->text) > 20 ? mb_substr($this->text, 0, 20) . '...' : $this->text;
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    public static function getAllSubcomments(int $questionId) : Collection
    {
        return Cache::remember('all_subcomments_' . $questionId, 3600, function () use ($questionId) {
            return Comment::active()
                ->where('question_id', $questionId)
                ->whereNotNull('comment_main_id')
                ->get(['id', 'comment_main_id']);
        });
    }

    public static function boot()
    {
        parent::boot();

        static::created(function ($item) {
            if (strtolower(config('notification.status')) !== 'off') {

                $notification = new CommentNotification($item->user, $item->comment);
                if (!CommentNotification::isExists($notification)) {
                    $item->comment->question->user->notify($notification);
                }
            }
        });

    }
}
