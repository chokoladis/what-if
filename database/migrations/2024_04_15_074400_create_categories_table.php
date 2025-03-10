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
        if (!Schema::hasTable('categories')) {
            Schema::create('categories', function (Blueprint $table) {
                $table->id();

                $table->unsignedBigInteger('parent_id')->nullable();
                $table->string('title');
                $table->string('code')->unique();

                $table->unsignedBigInteger('file_id')->nullable();
                $table->index('file_id', 'categories_file_idx');
                $table->foreign('file_id', 'categories_file_fk')->references('id')->on('files')->cascadeOnDelete();

                $table->smallInteger('level')->default(0)->nullable();
                $table->smallInteger('sort')->default(100)->nullable();
                $table->boolean('active')->nullable();

                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
