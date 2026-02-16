<?php

namespace App\Notifications;

use App\Interfaces\Services\UniqueDataNotifyInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

abstract class BaseNotification extends Notification
{
    use Queueable;

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['broadcast', 'database'];
    }

    public static function isExists(UniqueDataNotifyInterface $notification): bool
    {
        $query = \App\Models\Notification::query()
            ->where('type', get_class($notification));

        foreach ($notification->getUniqueData() as $key => $value) {
            $query->where('data->'.$key, $value);
        }

        return $query->select(['id'])->exists();
    }
}