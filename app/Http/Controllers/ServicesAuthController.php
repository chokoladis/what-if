<?php

namespace App\Http\Controllers;

use App\Services\Auth\GoogleAuthService;
use App\Services\Auth\YandexAuthService;
use Illuminate\Http\Request;
use Symfony\Component\Routing\Exception\InvalidParameterException;

class ServicesAuthController extends Controller
{
    public function googleAuth(Request $request)
    {
        $code = urldecode($request->get('code'));

        if (!empty($code)) {

            $result = (new GoogleAuthService())->authorize($code);

            if ($result === true){
                return redirect()->back()->with('message', __('user.login_success'));
            } else {
                return $result;
            }

        } else {
            throw new InvalidParameterException("Invalid code parameter");
        }
    }

    public function yandexAuth(Request $request)
    {
        $code = urldecode($request->get('code'));

        if (!empty($code)) {

            $result = (new YandexAuthService())->authorize($code);

            if ($result === true){
                return redirect()->back()->with('message', __('user.login_success'));
            } else {
                return $result;
            }

        } else {
            throw new InvalidParameterException("Invalid code parameter");
        }
    }

    public function telegramAuth($auth_data)
    {
        //removed
    }
}
