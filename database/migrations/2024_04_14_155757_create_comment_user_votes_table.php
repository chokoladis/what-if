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
        Schema::create('comment_user_votes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('comment_id');
            $table->index('comment_id', 'comment_user_votes_comment_idx');
            $table->foreign('comment_id', 'comment_user_votes_comments_fk')->references('id')->on('comments')->cascadeOnDelete();

            $table->unsignedBigInteger('user_id');
            $table->index('user_id', 'comment_user_votes_user_idx');
            $table->foreign('user_id', 'comment_user_votes_user_fk')->references('id')->on('users')->cascadeOnDelete();

            $table->tinyInteger('votes');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comment_user_votes');
    }
};
