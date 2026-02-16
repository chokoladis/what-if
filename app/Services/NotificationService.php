<?php

namespace App\Services;

use Illuminate\Notifications\DatabaseNotification;

final class NotificationService
{
    public static function toMessage(DatabaseNotification $notification)
    {
        $data = $notification->data;
        $fromUser = \App\Models\User::getNameById($data['from_user']['id']);

        if ($fromUser && $fromUser->name) {
            $username = $fromUser->name;
        } else {
            $username = $data['from_user']['name'];
        }

        $baseText = 'Пользователь - %s поставил лайк на <a href="%s" class="link-secondary link-offset-2 link-underline">%s</a>';

        if (strcmp($notification->type, \App\Notifications\Question\VoteNotification::class) === 0) {
            $title = 'Ваш вопрос лайкнули';
            $text = sprintf($baseText, $username, $data['question']['url'], 'вопрос - ' . $data['question']['title']);
        } elseif (strcmp($notification->type, \App\Notifications\Comment\VoteNotification::class) === 0) {
            $title = 'Ваш комментарий лайкнули';
            $text = sprintf($baseText, $username, $data['question']['url'], 'комментарий - ' . $data['comment']['text'] . ', в вопросе - ' . $data['question']['title']);
        } else {
            return null;
            // err and skip
        }

        return [
            'title' => $title,
            'text' => $text,
        ];
    }
}
