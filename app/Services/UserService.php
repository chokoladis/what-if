<?php

declare(strict_types=1);

namespace App\Services;

use App\Interfaces\AiApiInterface;
use App\Jobs\UserAvatarVerify;
use App\Models\Notification;
use App\Models\Question;
use App\Models\QuestionTags;
use App\Models\User;
use App\Models\UserTags;
use App\Services\AI\Gemini\AvatarValidatorService as AIAvatarValidator;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class UserService
{
    public function __construct(
        private ?AiApiInterface $AIAvatarValidator = null,
    )
    {
    }

    public static function getRecommendations(): LengthAwarePaginator
    {
        if (!Auth::id())
            abort(401);

        // from activity with question
        $questionWithLikes = Cache::remember('question_with_likes_u' . Auth::id(), 7200, function () {
            return Question::query()
                ->join('question_votes', 'questions.id', '=', 'question_votes.question_id')
                ->where('active', true)
                ->where('question_votes.user_id', Auth::id())
                ->where('question_votes.vote', 1)
                ->whereNot('questions.user_id', Auth::id())
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

        $userTags = UserTags::query()
            ->where('user_id', Auth::id())
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
                ->whereNot('questions.user_id', Auth::id())
                ->whereNotIn('questions.id', $questionIds)
                ->whereIn('question_tags.tag_id', $recommendByTags->pluck('tag_id'))
                ->inRandomOrder()
                ->select(['questions.*'])
                ->paginate();
        });
    }

    /**
     * @return Collection<int, Notification>
     */
    public static function getLastNotifications(): Collection
    {
        // todo foreget
        return Cache::remember('notify_' . Auth::id(), 86400, function () {
            return Auth::user()->notifications()
                ->limit(5)->latest()->get();
        });
    }

    public function setPhoto(UploadedFile $file): void
    {
        /** @var User $user */
        $user = Auth::user();
        if ($this->AIAvatarValidator && $this->AIAvatarValidator->isSetOn()) {
            $photo = FileService::saveTemp($file);

            UserAvatarVerify::dispatch($this->AIAvatarValidator, $user, $photo);
        } else {
            DB::beginTransaction();

            $photo = FileService::save($file, 'users');

            if (!$user->update(['photo_id' => $photo->id]))
                DB::rollBack();

            DB::commit();
        }
    }

    /**
     * @param array<string, string> $data
     * @return bool
     */
    public function update(array $data): bool
    {
        return Auth::user()->update($data);
    }

    public function getFullUserInfo(int $userId): ?User
    {
        return User::with(['questions', 'photo', 'tags'])->where('id', $userId)->first();
    }
}