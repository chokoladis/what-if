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
        Schema::create('comments', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id');
            $table->index('user_id', 'comments_user_idx');
            $table->foreign('user_id', 'comments_user_fk')->references('id')->on('users')->cascadeOnDelete();

            $table->unsignedBigInteger('question_id');
            $table->index('question_id', 'comments_question_idx');
            $table->foreign('question_id', 'comments_question_fk')->references('id')->on('questions')->cascadeOnDelete();

            $table->string('text', 1000);
            $table->boolean('active')->nullable()->default(false);

            $table->boolean('is_answer')->nullable()->default(false);
//            $table->index('right_comment_id', 'questions_right_comment_idx');
//            $table->foreign('right_comment_id', 'questions_comment_fk')->references('id')->on('comments')->cascadeOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
