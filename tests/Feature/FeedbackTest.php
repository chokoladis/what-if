<?php

namespace Feature;

use Faker\Generator as Faker;
use Illuminate\Http\Response;
use Tests\TestCase;

class FeedbackTest extends TestCase
{
    private Faker $faker;

    public function __construct(string $name)
    {
        $this->faker = \Faker\Factory::create();
//
        parent::__construct($name);
    }

    public function test_store_success()
    {
        $response = $this->post(route('feedback.store'), [
            'email' => $this->faker->email(),
            'phone' => $this->faker->phoneNumber(),
            'subject' => $this->faker->text(40),
            'comment' => $this->faker->text(),
        ]);

        $response->assertStatus(Response::HTTP_CREATED);
    }

    public function test_store_missing_comment()
    {
        $response = $this->post(route('feedback.store'), [
            'email' => $this->faker->email,
            'phone' => $this->faker->phoneNumber(),
            'subject' => $this->faker->text(40),
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_store_invalid_data()
    {
        $response = $this->post(route('feedback.store'), [
            'email' => 'mail.ss11@',
            'phone' => '+7' . rand(0, 10),
            'subject' => $this->faker->text(40),
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonPath('errors.0.field', 'email')
            ->assertJsonPath('errors.2.field', 'comment');
    }
}