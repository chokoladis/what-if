<?php

namespace Database\Seeders;

use App\Models\UserTags;
use Illuminate\Database\Seeder;

class UserTagsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        UserTags::factory(20)->create();
    }
}
