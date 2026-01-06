<?php

namespace App\View\Components\Question;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class SliderPopular extends Component
{
    public Collection $slides;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $this->slides = \App\Services\QuestionService::getPopular();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.question.slider-popular');
    }
}
