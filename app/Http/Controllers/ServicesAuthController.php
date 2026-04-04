<?php

namespace App\Http\Controllers;

use App\Services\Auth\GoogleAuthService;
use App\Services\Auth\YandexAuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ServicesAuthController extends Controller
{
    public function googleAuth(Request $request): RedirectResponse
    {
        if ($code = $request->get('code')) {

            $result = (new GoogleAuthService())->authorize($code);

            if ($result === true) {
                return redirect('/')->with('message', __('user.login_success'));
            } else {
                return $result;
            }

        } else {
//            todo commonerror ?
            return redirect()->back()->withErrors('error', 'Invalid code parameter');
        }
    }

    public function yandexAuth(Request $request): RedirectResponse
    {
        if ($code = $request->get('code')) {

            $result = (new YandexAuthService())->authorize($code);

            if ($result === true) {
                return redirect()->back()->with('message', __('user.login_success'));
            } else {
                return $result;
            }

        } else {
            return redirect()->back()->withErrors('error', 'Invalid code parameter');
        }
    }

//    todo with ngrok
//    public function telegramAuth($auth_data)
//    {
//    }
}
