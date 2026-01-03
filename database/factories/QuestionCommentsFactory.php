<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\Question;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\QuestionComments>
 */
class QuestionCommentsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $id = Question::query()->select('id')->inRandomOrder()->first()->id;

        return [
            'question_id' => $id,
            'comment_id' => Comment::factory(),
        ];
    }
}
