<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('question_comments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('question_id');
            $table->index('question_id', 'question_comments_question_idx');
            $table->foreign('question_id', 'question_comments_question_fk')->references('id')->on('questions')->cascadeOnDelete();

            $table->unsignedBigInteger('comment_id');
            $table->index('comment_id', 'question_comments_comment_idx');
            $table->foreign('comment_id', 'question_comments_comment_fk')->references('id')->on('comments')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('question_comments');
    }
};
