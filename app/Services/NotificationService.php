<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\Comment\VoteNotification;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

final class NotificationService
{
    const CACHE_KEY_5_LAST = 'notifications_5_last_';
    const CACHE_KEY_PAGINATE = 'notifications_paginate_';

    /**
     * @param DatabaseNotification $notification
     * @return array<string, string>
     */
    public static function toMessage(DatabaseNotification $notification): array
    {
        $data = $notification->data;
        if ($data['text']) {
            return [
                'title' => 'Системное уведомление',
                'text' => $data['text'],
            ];
        }

        $fromUser = User::getNameById($data['from_user']['id']);

        if ($fromUser && $fromUser->name) {
            $username = $fromUser->name;
        } else {
            $username = $data['from_user']['name'];
        }

        $baseText = 'Пользователь - %s поставил лайк на <a href="%s" class="link-secondary link-offset-2 link-underline">%s</a>';

        if (strcmp($notification->type, \App\Notifications\Question\VoteNotification::class) === 0) {
            $title = 'Ваш вопрос лайкнули';
            $text = sprintf($baseText, $username, $data['question']['url'], 'вопрос - ' . $data['question']['title']);
        } elseif (strcmp($notification->type, VoteNotification::class) === 0) {
            $title = 'Ваш комментарий лайкнули';
            $text = sprintf($baseText, $username, $data['question']['url'], 'комментарий - ' . $data['comment']['text'] . ', в вопросе - ' . $data['question']['title']);
        } else {
            $title = 'Системное уведомление';
            $text = 'null';
            // err and skip
        }

        return [
            'title' => $title,
            'text' => $text,
        ];
    }

//    todo in develop
    public static function getLastNotifications()
    {
        /** @var ?User $user */
        $user = Auth::user();

        if (!$user)
            return null;

        return Cache::remember(self::CACHE_KEY_5_LAST.$user->id, 3600, function () use ($user) {
            return $user->notifications()->limit(5)->get();
        });
    }

    public static function paginate()
    {
        $user = Auth::user();

        if (!$user)
            return null;

        return Cache::remember(self::CACHE_KEY_PAGINATE.$user, 3600, function () {
            return Auth::user()->notifications()->latest()->paginate(10);
        });
    }
}
