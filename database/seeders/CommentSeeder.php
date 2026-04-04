<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\CommentVotes;
use App\Models\Question;
use App\Models\QuestionVotes;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Comment::factory(30)->create();
        CommentVotes::factory()->count(50)->create();
    }
}
