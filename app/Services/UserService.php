<?php

namespace App\Services;

use App\Models\Question;
use App\Models\QuestionTags;
use App\Models\UserTag;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class UserService
{
    public static function getRecommendations()
    {
        if (!auth()->id())
            abort(401);

        // from activity with question
        $questionWithLikes = Cache::remember('question_with_likes_u'.auth()->id(), 7200, function () {
            return Question::query()
                ->join('question_votes', 'questions.id', '=', 'question_votes.question_id')
                ->where('active', true)
                ->where('question_votes.user_id', auth()->id())
                ->where('question_votes.vote', 1)
                ->whereNot('questions.user_id', auth()->id())
                ->select(['questions.id', 'category_id'])
                ->get();
        });

        $questionIds = $categoryIds = [];
        foreach ($questionWithLikes as $question) {
            $questionIds[] = $question->id;
            if (!in_array($question->category_id, $categoryIds)) {
                $categoryIds[] = $question->category_id;
            }
        }

        $tags = QuestionTags::query()
            ->whereIn('question_id', $questionIds)
            ->distinct()
            ->get(['tag_id']);

        $userTags = UserTag::query()
            ->where('user_id', auth()->id())
            ->whereNotIn('tag_id', $tags)
            ->select('tag_id')
            ->get();
        $recommendByTags = new Collection;
        $recommendByTags = $recommendByTags->merge($tags);
        $recommendByTags = $recommendByTags->merge($userTags);

        //        todo recommend by $categoryIds

        return Cache::remember('question_recommendations', 7200, function () use ($questionIds, $recommendByTags) {
            return Question::query()
                ->join('question_tags', 'questions.id', '=', 'question_tags.question_id')
                ->where('active', true)
                ->whereNot('questions.user_id', auth()->id())
                ->whereNotIn('questions.id', $questionIds)
                ->whereIn('question_tags.tag_id', $recommendByTags->pluck('tag_id'))
                ->inRandomOrder()
                ->select(['questions.*'])
                ->paginate();
        });
    }
}