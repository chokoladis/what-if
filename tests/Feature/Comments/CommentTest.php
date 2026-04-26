<?php

namespace Feature\Comments;


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

    public function test_set_right_comment_success(): void
    {
        $question = Question::factory()->create([
            'active' => true,
        ]);
        $comment = Comment::factory()->create([
            'question_id' => $question->id,
            'active' => true,
            'is_answer' => true,
        ]);

        $response = $this->get(route('questions.detail', [$question->code]));
        $response->assertOk()
            ->assertSeeHtml(sprintf('data-comment-id="%d" class="comment is-answer"', $comment->id));
    }

    public function test_set_right_comment_no_active(): void
    {
        $question = Question::factory()->create([
            'active' => true,
        ]);
        $commentAnswer = Comment::factory()
            ->for($question)
            ->create([
                'active' => false,
                'is_answer' => true,
            ]);
        $commentUsually = Comment::factory()
            ->for($question)
            ->create([
                'active' => true,
            ]);

        $response = $this->get(route('questions.detail', [$question->code]));
        $response->assertOk()
            ->assertSeeHtml(sprintf('data-comment-id="%d"', $commentUsually->id))
            ->assertDontSeeHtml(sprintf('data-comment-id="%d"', $commentAnswer->id));
    }
}
