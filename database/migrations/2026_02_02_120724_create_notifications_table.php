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
        if (!Schema::hasTable('notifications')) {
            Schema::create('notifications', function (Blueprint $table) {
                $table->id();

                $table->unsignedBigInteger('user_id')->index('user_id');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

                $table->string('entity', 15);
                $table->unsignedBigInteger('entity_id')->index('entity_id');
                $table->string('type', 30)->default('system');
                $table->boolean('viewed')->default(0);

                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
