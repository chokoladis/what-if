<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\Component;

class ProfileNotification extends Component
{
    /* model - DatabaseNotification */
    public LengthAwarePaginator $notifications;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        //        todo check paginate
        $this->notifications = auth()->user()->notifications()->latest()->paginate();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.profile-notification');
    }
}
