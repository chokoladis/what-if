<?php

namespace App\Http\Controllers;

use App\Http\Requests\Comment\UserStatusStoreRequest;
use App\Models\CommentUserStatus;

class CommentUserStatusController extends Controller
{
    public function setStatus(UserStatusStoreRequest $request)
    {
        $data = $request->validated();
        $comment_id = intval($data['comment_id']);
        $action = intval($data['action']);

        $commentStatus = CommentUserStatus::query()
            ->where('comment_id', $comment_id)
            ->where('user_id', auth()->id())
            ->where('status', $action)
            ->first();

        if ($commentStatus->exists) {
            $commentStatus->delete();

            return true;
        }

        CommentUserStatus::updateOrCreate([
            'comment_id' => $comment_id, 'user_id' => auth()->id()
        ], [
            'status' => $action
        ]);

        return true;
    }
}
