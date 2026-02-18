<?php

namespace App\Http\Controllers;

use App\Exceptions\FileSaveException;
use App\Exceptions\FileValidationException;
use App\Http\Requests\User\SetPhotoRequest;
use App\Http\Requests\User\SetTagsRequest;
use App\Http\Requests\User\UpdateRequest;
use App\Models\Tag;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    function __construct()
    {
        $this->middleware('throttle:4,1')->only('update', 'setPhoto');
    }

    public function index()
    {
        $userTags = auth()->user()->tags()->get();
        $tags = Tag::query()->whereNotIn('id', $userTags->pluck('id'))->get();
        $notifications = \App\Services\UserService::getLastNotifications();

        return view('profile.index', compact('tags', 'userTags', 'notifications'));
    }

    public function edit()
    {
        return view('profile.edit');
    }

    public function update(UpdateRequest $request)
    {

        $data = $request->validated();
        $user = User::find(auth()->id());
        if ($user->update($data)) {
            return redirect()->route('profile.index')->with('message', __('system.alerts.success'));
        }

        return redirect()->route('profile.index')->with('error', __('system.alerts.error'));
    }

    public function setPhoto(SetPhotoRequest $request)
    {
        $file = $request->file('photo');
        if ($file->getError()) {
            return redirect()->route('profile.index')->with('error', $file->getErrorMessage());
        }

        $service = new UserService();
        try {
            $service->setPhoto($file);
        } catch (FileValidationException|FileSaveException $e) {
            return redirect()->route('profile.index')->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return redirect()->route('profile.index')->with('error', 'system_error');
        }

        return redirect()->route('profile.index')->with('message', __('system.alerts.success'));
    }

    public function setTags(SetTagsRequest $request)
    {
        $data = $request->validated();
        $res = auth()->user()->tags()->sync(isset($data['tags']) ? $data['tags'] : []);

        if (!empty($res)) {
            return redirect()->route('profile.index')->with('message', __('system.alerts.success'));
        }

        return redirect()->route('profile.index')->with('error', __('system.alerts.error'));
    }
}
