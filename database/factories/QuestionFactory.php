<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\File;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Question>
 */
class QuestionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(),
            'category_id' => Category::factory(),
            'file_id' => File::factory()->forQuestion(),
            'user_id' => $this->faker->numberBetween(1, 3),
            'active' => $this->faker->boolean(),
        ];
    }
}
