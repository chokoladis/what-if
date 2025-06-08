<?php

return [
    'questions' => [
        'fields' => [
            'category' => 'Category',
            'title' => 'Title',
            'img' => 'Picture',
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
        'fields' => [
            'email' => 'Email',
            'phone' => 'Телефон',
            'subject' => 'Тема',
            'comment' => 'Комментарий'
        ]
    ],
    'users' => [
        'fields' => [
            'name' => 'Имя',
            'photo' => 'Фото',
        ]
    ]
];

?>