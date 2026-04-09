<?php

namespace App\Listeners;

use App\Events\ViewEvent;
use Illuminate\Support\Facades\Auth;

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

        $user_id = (string)(Auth::id() ?? request()->ip());
        $user_id = $user_id ? (string)str_replace('.', '_', $user_id) : 'undefined';

        // todo save to db for recommendations
        $name_session = 'view_user_' . $user_id . '_model_' . $model->getTable() . '_' . $model->id;
        if (!session($name_session)) {
            session([$name_session => true]);

            if ($event->model->statistics) {
                $event->model->statistics->increment('views');
            }
        }
    }
}
