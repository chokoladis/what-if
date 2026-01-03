<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\File>
 */
class FileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $ext = $this->faker->randomElement(['png', 'jpg', 'jpeg']);
        $name = $this->faker->uuid . '.' .$ext;

        $dirName = substr($name, 0, 3);

        return [
            'name' => $name,
            'expansion' => $ext,
            'path' => $dirName.'/'.$name,
        ];
    }

    public function forCategory(): static
    {
        return $this->state([
            'relation' => 'categories',
        ]);
    }

    public function forQuestion(): static
    {
        return $this->state([
            'relation' => 'questions',
        ]);
    }
}
