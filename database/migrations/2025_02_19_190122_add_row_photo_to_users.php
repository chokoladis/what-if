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
        if (!Schema::hasColumn('users', 'photo_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedBigInteger('photo_id')
                    ->nullable()->after('active')
                    ->index('users_photo_idx');
                $table->foreign('photo_id', 'users_files_fk')->references('id')->on('files');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('users', 'photo_id')) {
            Schema::dropForeign('users_files_fk');
            Schema::dropColumns('users', 'photo_id');
        }
    }
};
