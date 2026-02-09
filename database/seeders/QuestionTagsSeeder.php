<?php

namespace Database\Seeders;

use App\Models\QuestionTags;
use Illuminate\Database\Seeder;

class QuestionTagsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        QuestionTags::factory(30)->create();
    }
}
