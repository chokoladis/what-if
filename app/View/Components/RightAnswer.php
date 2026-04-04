<?php

namespace App\View\Components;

use App\Models\Comment;
use App\Services\FileService;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class RightAnswer extends Component
{
    public string $imgSrc;
    public string $userName;
    public string $text;

    /**
     * Create a new component instance.
     * @param Comment|mixed $comment
     */
    public function __construct(
        public mixed $comment,
        public bool  $compact = false,
    )
    {
        if (is_array($comment)) {
            $this->imgSrc = FileService::getPhotoFromIndex($comment['user']['photo'], 'users');
            $this->userName = $comment['user']['name'];
            $this->text = mb_strlen($comment['text']) > 60 ? mb_substr($comment['text'], 0, 60) : $comment['text'];
        } else {
            $this->imgSrc = FileService::getPhoto($comment->user->file);
            $this->userName = $comment->user->name;
            $this->text = mb_strlen($comment->text) > 60 ? mb_substr($comment->text, 0, 60) : $comment->text;
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.right-answer');
    }
}
