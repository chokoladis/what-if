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
        if (!Schema::hasTable('question_tags')) {
            Schema::create('question_tags', function (Blueprint $table) {
                $table->id();

                $table->unsignedBigInteger('question_id');
                $table->index('question_id');
                $table->foreign('question_id')->references('id')->on('questions')->onDelete('cascade');

                $table->unsignedBigInteger('tag_id');
                $table->index('tag_id');
                $table->foreign('tag_id')->references('id')->on('tags')->onDelete('cascade');

                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('question_tags');
    }
};
