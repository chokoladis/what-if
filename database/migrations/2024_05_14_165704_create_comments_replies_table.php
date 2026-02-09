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
        Schema::create('comments_replies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('comment_main_id');
            $table->index('comment_main_id', 'comments_replies_comment_main_idx');
            $table->foreign('comment_main_id', 'comments_replies_comments_fk_main')->
                references('id')->on('comments')->cascadeOnDelete();

            $table->unsignedBigInteger('comment_reply_id');
            $table->index('comment_reply_id', 'comments_replies_comment_reply_idx');
            $table->foreign('comment_reply_id', 'comments_replies_comments_fk_reply')->
                references('id')->on('comments')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments_replies');
    }
};
