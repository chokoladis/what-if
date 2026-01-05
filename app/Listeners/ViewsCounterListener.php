<?php

namespace App\Listeners;

use App\Events\ViewEvent;

class ViewsCounterListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ViewEvent $event): void
    {
        $model = $event->model;

        $user_id = auth()->id() ?? request()->ip();
        $user_id = str_replace('.', '_', $user_id);

        $name_session = 'view_user_' . $user_id . '_model_' . $model->getTable() . '_' . $model->id;
        $session_user_view = session($name_session);
        if (!$session_user_view) {
            session([$name_session => true]);

            if ($event->model->statistics) {
                $event->model->statistics->increment('views');
            }
        }
    }
}
