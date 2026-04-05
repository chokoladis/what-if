<?php

namespace Feature\Comments;


use App\Models\Category;
use App\Models\Comment;
use App\Models\Question;
use Tests\TestCase;

class CommentTest extends TestCase
{
//    todo store, sumbcomments
    public function test_question_with_comments(): void
    {
        $question = Question::factory()->create([
            'active' => true,
        ]);
        Comment::factory()->create([
            'active' => true,
        ]);

        $response = $this->get(route('questions.detail', [$question->code]));

        $response->assertOk()->assertSeeHtml('data-comment-id');
    }
}
