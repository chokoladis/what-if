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
        $user->update($data);

        return redirect()->route('profile.index');
    }

    public function setPhoto(SetPhotoRequest $request)
    {
        $file = $request->file('photo');

        if ($file->getError()){
            return $file->getErrorMessage();
        }

        $photo = FileService::save($file, 'users');

//        todo on stack
//        $isLegal = (new UserService)->isContentFileLegal($photo);
        $isLegal = (new UserService())->isContentFileLegal($photo);
        dd($isLegal);

        $user = User::find(auth()->id());
        $user->photo_id = $photo->id;
        $user->save();

        return redirect()->route('profile.index');
    }
}
