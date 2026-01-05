<?php

return [
    'meilisearch' => [
        'host' => env('MEILISEARCH_HOST', 'http://localhost:7700'),
        'key' => env('MEILISEARCH_KEY'),
        'index-settings' => [
            \App\Models\Question::class => [
                'filterableAttributes' => ['category_id', 'category_list'],
                'sortableAttributes' => ['title', 'created_at'],
            ],
            \App\Models\Category::class => [
                'sortableAttributes' => ['title', 'count_question', 'created_at'],
            ],
        ],
    ],
];