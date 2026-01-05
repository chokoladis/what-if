<?php

namespace App\Console\Commands\Meilisearch\Question;

use Illuminate\Console\Command;
use Meilisearch\Client;

class UpdateFilterable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app-meilisearch:question-update-filterable';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'For category_id, category_list in questions index';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $client = new Client(config('scout.meilisearch.host'), config('scout.meilisearch.api_key'));
        $client->index('questions')->updateFilterableAttributes(['category_id', 'category_list']);
    }
}
