<?php

namespace App\Http\Controllers;

use App\Exceptions\FileSaveException;
use App\Exceptions\FileValidationException;
use App\Http\Requests\User\SetPhotoRequest;
use App\Http\Requests\User\SetTagsRequest;
use App\Http\Requests\User\UpdateRequest;
use App\Models\User;
use App\Services\TagService;
use App\Services\UserService;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;

class UserController extends Controller
{
    public TagService $tagService;
    public UserService $userService;

    function __construct()
    {
        $this->tagService = new TagService();
        $this->userService = new UserService();

        $this->middleware('throttle:4,1')->only('update', 'setPhoto');
    }

    public function index(): View
    {
        $userId = (int)Auth::id();
        if (!$userId)
            abort(403);

        $user = $this->userService->getFullUserInfo($userId);
        if (!$user)
            abort(404);

        $tagsNotChecked = $this->tagService->getNotSelected($user->tags()->pluck('tags.id'));
        //todo questions
        $notifications = UserService::getLastNotifications();

        return view('profile.index', compact('user', 'tagsNotChecked', 'notifications'));
    }

    public function update(UpdateRequest $request): RedirectResponse
    {
        if ($this->userService->update($request->validated())) {
            return redirect()->route('profile.index')->with('message', __('system.alerts.success'));
        }

        return redirect()->route('profile.index')->withErrors('error', __('system.alerts.error'));
    }

    public function setPhoto(SetPhotoRequest $request): RedirectResponse
    {
        /** @var UploadedFile $file */
        $file = $request->file('photo');
        if ($file->getError()) {
            return redirect()->route('profile.index')->with('error', $file->getErrorMessage());
        }

        try {
            $this->userService->setPhoto($file);
        } catch (FileValidationException|FileSaveException $e) {
            return redirect()->route('profile.index')->with('error', $e->getMessage());
        } catch (Throwable $e) {
            Log::error($e->getMessage());
            return redirect()->route('profile.index')->with('error', 'system_error');
        }

        return redirect()->route('profile.index')->with('message', __('system.alerts.success'));
    }

    public function setTags(SetTagsRequest $request): RedirectResponse
    {
        try {
            $data = $request->validated();
            /** @var User $user */
            $user = Auth::user();
            $user->tags()->sync(isset($data['tags']) ? $data['tags'] : []);

            return redirect()->route('profile.index')->with('message', __('system.alerts.success'));
        } catch (Exception $e) {
            return redirect()->route('profile.index')->with('error', __('system.alerts.error'));
        }
    }
}
