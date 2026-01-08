<?php

return [
    'questions' => [
        'fields' => [
            'category' => 'Category',
            'title' => 'Title',
            'img' => 'Picture',
            'tag' => 'Tags',
        ],
        'placeholders' => [
            'title' => 'Why do gypsies wash themselves but still smell?'
        ]
    ],
    'categories' => [
        'fields' => [
            'parent_id' => 'Parent category',
            'title' => 'Title',
            'sort' => 'Sort',
            'img' => 'Picture',
        ]
    ],
    'comments' => [
        'fields' => [
            'text' => 'Коммент',
            'comment_reply' => 'Вы ответите по комметарию'
        ]
    ],
    'feedback' => [
        'title_modal' => 'Feedback',
        'fields' => [
            'email' => 'Email',
            'phone' => 'Phone',
            'subject' => 'Subject',
            'comment' => 'Comment'
        ],
        'options_subject' => [

        ]
    ],
    'users' => [
        'fields' => [
            'name' => 'Name',
            'photo' => 'Photo',
        ]
    ]
];

?>