<?php

namespace Tests\Feature;

use GuzzleHttp\Psr7\UploadedFile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class QuestionTest extends TestCase
{
    public function test_store_status_422(): void
    {
        $this->withoutMiddleware();

        $response = $this->postJson('/questions', [
            'category' => '0',
            'title' => '1',
        ]);
        $response->assertStatus(422)->assertJsonValidationErrors(['title']);
    }

    public function test_store_status_200(): void
    {
        $this->withoutMiddleware();

        $response = $this->postJson('/questions', [
            'category' => '0',
            'title' => '123_2',
        ]);

        $response->assertStatus(200); //if created - status 302
    }

    public function test_store_invalid_img(): void
    {
        $this->withoutMiddleware();

        $response = $this->postJson('/questions', [
            'title' => '1235',
            'img' => 'test'
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors(['img']);
    }


    public function test_store_valid_img(): void
    {
        $this->withoutMiddleware();

        Storage::fake('public');

        $file = \Illuminate\Http\UploadedFile::fake()->image('test_img.jpg', 1200, 800);

        $response = $this->postJson('/questions', [
            'title' => '123_test331',
            'img' => $file
        ]);

        $response->assertStatus(302);
    }
}
