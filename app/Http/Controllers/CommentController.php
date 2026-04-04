<?php

namespace App\Http\Controllers;

use App\Http\Requests\Comment\StoreRequest;
use App\Http\Requests\Question\LoadSubcommentsRequest;
use App\Models\CommentsReply;
use App\Models\QuestionComments;
use App\Services\CommentService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class CommentController extends Controller
{
    private CommentService $commentService;

    public function __construct()
    {
        $this->commentService = new CommentService();
    }

    public function store(StoreRequest $request): RedirectResponse
    {
        $comment = $this->commentService->save($request->validated());
        if ($comment->wasRecentlyCreated) {
            return redirect()->back()->with('message', __('system.alerts.success'));
        } else {
            return redirect()->back()->withErrors('error', 'Вы уже оставляли такой комментарий');
        }
    }

    public function loadSubcomments(LoadSubcommentsRequest $request): View|false
    {
        $children = $this->commentService->getChildren($request->validated());

        if ($children->isNotEmpty()) {
            return view('components.comment.subcomments', compact('children'));
        }

        return false;
    }
}
