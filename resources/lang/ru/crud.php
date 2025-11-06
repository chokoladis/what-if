<?php

return [
    'questions' => [
        'fields' => [
            'category' => 'Раздел',
            'title' => 'Заголовок',
            'img' => 'Прикрепляемая картинка',
        ]
    ],
    'categories' => [
        'fields' => [
            'parent_id' => 'Родительский раздел',
            'title' => 'Заголовок',
            'sort' => 'Сортировка',
            'img' => 'Прикрепляемая картинка',
        ]
    ],
    'comments' => [
        'fields' => [
            'text' => 'Коммент',
            'comment_reply' => 'Вы ответите по комметарию'
        ]
    ],
    'feedback' => [
        'title_modal' => 'Обратная связь',
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