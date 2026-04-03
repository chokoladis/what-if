<?php

namespace Tests\Feature;


use App\Models\Question;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class QuestionTest extends TestCase
{
    public function test_store_success_create(): void
    {
        $this->withoutMiddleware();

        $response = $this->actingAs(User::factory()->create())->postJson('/questions', [
            'category' => '0',
            'title' => 'test_store_success_create',
        ]);

        $response->assertRedirect(route('questions.index'));
    }

    public function test_store_status_422(): void
    {
        $this->withoutMiddleware();

        $response = $this->actingAs(User::factory()->create())->postJson('/questions', [
            'category' => '0',
            'title' => '1',
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors(['title']);
    }

    public function test_store_invalid_img(): void
    {
        $this->withoutMiddleware();

        $response = $this->actingAs(User::factory()->create())->postJson('/questions', [
            'title' => '1235',
            'img' => 'test'
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['img']);
    }


    public function test_store_valid_img(): void
    {
        $this->withoutMiddleware();

        Storage::fake('public');
        $file = UploadedFile::fake()->image('test_img.jpg', 1200, 800);

        $response = $this->actingAs(User::factory()->create())->postJson('/questions', [
            'title' => 'test_store_valid_img',
            'img' => $file
        ]);

        $response->assertStatus(302);
    }

    public function test_index_page_ok(): void
    {
        Question::factory()->create([
            'active' => true
        ]);

        $response = $this->get(route('questions.index'));
        $response->assertOk()->assertSeeHtml('class="item card mb-3 "');
    }
}
