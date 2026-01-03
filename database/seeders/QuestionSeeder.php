<?php

namespace Database\Seeders;

use App\Models\Question;
use App\Models\QuestionComments;
use App\Models\QuestionVotes;
use Illuminate\Database\Seeder;

class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
//        Question::factory(10)->create();
        QuestionComments::factory(10)->create();
        QuestionVotes::factory(10)->create();
    }
}
