<?php

namespace App\Services;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class CommentService
{
    /**
     * @param array<string, string|int> $data
     * @return Comment|null
     */
    public function save(array $data) : ?Comment
    {
        /** @var User $user */
        $user = Auth::user();
        $data['user_id'] = $user->id;
        $data['active'] = $user->role === 'admin'; //todo gate ?

        return Comment::firstOrCreate(['text' => $data['text'], 'user_id' => $data['user_id']], $data);
    }

    /**
     * @param array<string, int> $data
     * @return Collection
     */
    public function getChildren(array $data): Collection
    {
        $offset = $data['offset'] ?? 0;

        return Cache::remember('comment_children_' . serialize($data), 3600, function () use ($data, $offset) {
            return Comment::query()
                ->with('question')
                ->where('comment_main_id', $data['comment-id'])
                ->skip($offset)
                ->take(Comment::DEFAULT_LIMIT)
                ->get();
        });
    }
}