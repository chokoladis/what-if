<?php

namespace Database\Factories;

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
        $id = Question::query()->select('id')->inRandomOrder()->first()->id;
        $userId = \App\Models\User::query()->select('id')->inRandomOrder()->first()->id;

        return [
            'question_id' => $id,
            'user_id' => $userId,
            'vote' => $this->faker->randomElement([-1, 1]),
        ];
    }
}
