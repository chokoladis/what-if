<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\Question;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\QuestionComments>
 */
class CommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $parentComment = $this->faker->randomElement(
            array_merge(
                Comment::where('active', true)->get(['id', 'question_id'])->toArray(),
                [null]
            )
        );

        return [
            'question_id' => $parentComment
                ? $parentComment['question_id']
                : Question::select('id')->inRandomOrder()->first()->id,
            'user_id' => User::select('id')->inRandomOrder()->first()->id,
            'comment_main_id' => $parentComment ? $parentComment['id'] : null,
            'text' => $this->faker->realText(),
            'active' => $this->faker->boolean(),
        ];
    }
}
