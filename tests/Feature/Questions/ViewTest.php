<?php

namespace Feature\Questions;

use App\Models\Question;
use Tests\TestCase;

class ViewTest extends TestCase
{
    public function test_index_page_ok(): void
    {
        Question::factory()->create([
            'active' => true
        ]);

        $response = $this->get(route('questions.index'));
        $response->assertOk()->assertSeeHtml('class="item card mb-3 "');
    }

    public function test_detail_page_ok(): void
    {
        $question = Question::factory()->create([
            'active' => true
        ]);

        $response = $this->get(route('questions.detail', [$question->code]));
        $response->assertOk()->assertSeeHtml('class="icon like btn');
    }

    public function test_detail_page_not_active(): void
    {
        $question = Question::factory()->create([
            'active' => false
        ]);

        $response = $this->get(route('questions.detail', [$question->code]));
        $response->assertViewIs('errors.404');
    }
}