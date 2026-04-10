<?php

namespace App\Services;

use App\Models\Comment;
use App\Models\CommentVotes;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class CommentService
{
    /**
     * @param array<string, string|int> $data
     * @return Comment
     */
    public function save(array $data): Comment
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

    /**
     * @param int $questionId
     * @param array<string, int|string> $params
     * @return Collection
     */
    public function getWithPagination(int $questionId, array $params = []): Collection
    {
        $params['page'] = intval($params['page'] ?? 1);
        $offset = Comment::DEFAULT_LIMIT * ($params['page'] - 1);
        $sortBy = $params['sortBy'] ?? 'votes_sum_vote';
        $order = $params['order'] ?? 'desc';

        // todo проверять популярность ещё по вложенным
        return Cache::remember('comments_with_paginate_question_' . $questionId, 3600, function ()
        use ($questionId, $offset, $sortBy, $order) {
            return Comment::active()
                ->where('question_id', $questionId)
                ->whereNull('comment_main_id') // не вложенные
                ->with('user')
                ->withSum('votes', 'vote')
                ->limit(Comment::DEFAULT_LIMIT)
                ->offset($offset)
                ->orderBy($sortBy, $order)
                ->get();
        });
    }

    /**
     * @param array<int, int> $ids
     * @return array<int, mixed>|null
     */
    public function getVotesCurrentUserByIds(array $ids): ?array
    {
        if (Auth::id()) {
            return CommentVotes::query()
                ->whereIn('comment_id', $ids)
                ->where('user_id', Auth::id())
                ->get(['id', 'vote'])
                ->toArray();
        }

        return null;
    }

    /**
     * @param int $questionId
     * @return array<int, int>
     */
    public function getTotalCountSubcomments(int $questionId)
    {
        $arCount = [];

        $allComments = Comment::getAllSubcomments($questionId);
        foreach ($allComments->keys() as $key) {

            $comment = $allComments[$key];

            if (!empty($arCount[$comment->comment_main_id])) {
                $arCount[$comment->comment_main_id]++;
            } else {
                $arCount[$comment->comment_main_id] = 1;
            }

            $allComments->forget($key);
        }

        return $arCount;
    }
}