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
        Schema::create('question_user_votes', function (Blueprint $table) {
            $table->id();
            
            $table->unsignedBigInteger('question_id');
            $table->index('question_id', 'question_user_votes_question_idx');
            $table->foreign('question_id', 'question_user_votes_question_fk')->references('id')->on('questions')->cascadeOnDelete();

            $table->unsignedBigInteger('user_id');
            $table->index('user_id', 'question_user_votes_user_idx');
            $table->foreign('user_id', 'question_user_votes_user_fk')->references('id')->on('users')->cascadeOnDelete();

            $table->tinyInteger('vote');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('question_user_votes');
    }
};
