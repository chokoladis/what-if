<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\SetPhotoRequest;
use App\Http\Requests\User\UpdateRequest;
use App\Models\User;
use App\Services\AI\Gemini\UserService;
use App\Services\FileService;

class UserController extends Controller
{
    function __construct()
    {
        $this->middleware('throttle:4,1')->only('update', 'setPhoto');
    }

    public function index(){
        return view('profile.index');
    }

    public function update(UpdateRequest $request){

        $data = $request->validated();
        $user = User::find(auth()->id());
        if ($user->update($data)){
            return redirect()->route('profile.index')->with('message', __('system.alerts.success'));
        }

        return redirect()->route('profile.index')->with('error', __('system.alerts.error'));
    }

    public function setPhoto(SetPhotoRequest $request)
    {
        $file = $request->file('photo');

        if ($file->getError()){
            return redirect()->route('profile.index')->with('error', $file->getErrorMessage());
        }

        $photo = FileService::save($file, 'users');

//        todo on stack
         [$isLegal, $error] = (new UserService())->isContentFileLegal($photo);

        if (!$isLegal){
            return redirect()->route('profile.index')->with('error', $error);
        }

        $user = User::find(auth()->id());
        $user->photo_id = $photo->id;
        $user->save();

        return redirect()->route('profile.index')->with('message', __('system.alerts.success'));
    }
}
