<?php

namespace Database\Factories;

use App\Enums\Vote;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\QuestionComments>
 */
class CommentVotesFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'comment_id' => Comment::query()->where('active', true)->inRandomOrder()->first(),
            'user_id' => User::query()->inRandomOrder()->select('id')->first()->id,
            'vote' => $this->faker->randomElement(Vote::cases())->value,
        ];
    }
}
