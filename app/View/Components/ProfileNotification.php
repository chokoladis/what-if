<?php

namespace App\View\Components;

use App\Models\Notification;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ProfileNotification extends Component
{
    
    /**
     * Create a new component instance.
     */
    public function __construct(
        Notification $notification,
    )
    {
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.profile-notification');
    }
}
