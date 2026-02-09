<?php

namespace Database\Factories;

use App\Enums\Vote;
use App\Models\Question;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\QuestionVotes>
 */
class QuestionVotesFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'question_id' => Question::query()->select('id')->inRandomOrder()->first()->id,
            'user_id' => \App\Models\User::query()->select('id')->inRandomOrder()->first()->id,
            'vote' => Vote::cases()[rand(0, 1)]->value,
        ];
    }
}
