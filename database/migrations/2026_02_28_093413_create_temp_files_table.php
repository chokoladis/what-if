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
        // relation to queue
        Schema::create('temp_files', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('expansion', 5);
            $table->string('path')->unique();

//            $table->string('description')->nullable(); //todo is need ?

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temp_files');
    }
};
