<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ServicesAuthController extends Controller
{
    const YANDEX_LINK_PICTURE = 'https://avatars.mds.yandex.net/get-yapic/';

    public function googleAuth(Request $request)
    {

        $code = urldecode($request->get('code'));

        if (!empty($code)) {

            if ($data = $this->getGoogleUserToken($code)) {

                $userData = $this->getGoogleUserInfo($data);

                $user = User::query()
                    ->where('email', $userData['email'])
                    ->first();

                if (!$user) {
                    $password = Str::random(12);

                    $newUser = User::create([
                        'name' => $userData['name'],
                        'email' => $userData['email'],
                        'password' => $password,
                        'active' => 1,
                        'profile_photo_path' => $userData['picture'],
                    ]);

                    $user = $newUser;
                    // send psw on email
                }

                Auth::login($user);

            } else {
                exit;
            }

        } else {
            throw new Exception("Ошибка параметров");
        }

        return redirect()->back()->with('message', __('user.login_success'));
    }

    public function getGoogleUserToken(string $code)
    {

        try {
            $params = array(
                'client_id' => config('auth.socials.google.client_id'),
                'client_secret' => config('auth.socials.google.client_secret'),
                'redirect_uri' => config('auth.socials.google.redirect_uri'),
                'grant_type' => 'authorization_code',
                'code' => $code
            );

            $ch = curl_init('https://accounts.google.com/o/oauth2/token');
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HEADER, false);
            $data = curl_exec($ch);
            curl_close($ch);

            $data = json_decode($data, true);

            if (!empty($data['access_token'])) {
                return $data;
            } elseif (isset($data['error']) && !empty($data['error'])) {
                echo 'error -> ' . $data['error'];
            } else {
                echo 'Неизвестная ошибка<br>';
                print_r($data);
            }

        } catch (\Throwable $th) {
            throw $th;
        }

        return false;
    }

    public function getGoogleUserInfo(array $data)
    {

        $params = array(
            'access_token' => $data['access_token'],
            'id_token' => $data['id_token'],
            'token_type' => 'Bearer',
            'expires_in' => 3599
        );

        $info = file_get_contents('https://www.googleapis.com/oauth2/v1/userinfo?' . urldecode(http_build_query($params)));
        $info = json_decode($info, true);

        return $info;
    }

    public function yandexAuth(Request $request)
    {

        $code = urldecode($request->get('code'));

        if (!empty($code)) {

            if ($data = $this->getYandexUserToken($code)) {

                $userData = $this->getYandexUserInfo($data);

                $user = User::query()
                    ->where('email', $userData['default_email'])
                    ->first();

                if (!$user) {
                    $password = Str::random(12);

                    $newUser = User::create([
                        'name' => $userData['real_name'],
                        'email' => $userData['default_email'],
                        'password' => $password,
                        'active' => 1,
                        'profile_photo_path' => self::YANDEX_LINK_PICTURE . $userData['default_avatar_id'],
                    ]);

                    $user = $newUser;
                    // send psw on email
                }

                Auth::login($user);

            } else {
                exit;
            }

        } else {
            throw new Exception("Ошибка параметров");
        }

        return redirect()->back()->with('message', __('user.login_success'));
    }

    public function getYandexUserToken(string $code)
    {

        try {

            $fields = array(
                'grant_type' => 'authorization_code',
                'code' => $code,
                'client_id' => config('auth.socials.yandex.client_id'),
                'client_secret' => config('auth.socials.yandex.client_secret'),
            );

            $ch = curl_init('https://oauth.yandex.ru/token');
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HEADER, false);
            $data = curl_exec($ch);
            curl_close($ch);

            $data = json_decode($data, true);

            if (!empty($data['access_token'])) {
                return $data;
            } elseif (isset($data['error']) && !empty($data['error'])) {
                echo 'error -> ' . $data['error'];
            } else {
                echo 'Неизвестная ошибка<br>';
                print_r($data);
            }

        } catch (\Throwable $th) {
            throw $th;
        }

        return false;
    }

    public function getYandexUserInfo(array $data)
    {

        $ch = curl_init('https://login.yandex.ru/info');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, array('format' => 'json'));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: OAuth ' . $data['access_token']));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $info = curl_exec($ch);
        curl_close($ch);

        $info = json_decode($info, true);
        return $info;
    }

    public function telegramAuth($auth_data)
    {

        $check_hash = $auth_data['hash'];
        unset($auth_data['hash']);
        $data_check_arr = [];
        foreach ($auth_data as $key => $value) {
            $data_check_arr[] = $key . '=' . $value;
        }
        sort($data_check_arr);
        $data_check_string = implode("\n", $data_check_arr);

        $secret_key = hash('sha256', config('auth.socials.telegram.bot_token'), true);
        $hash = hash_hmac('sha256', $data_check_string, $secret_key);
        if (strcmp($hash, $check_hash) !== 0) {
            throw new Exception('Data is NOT from Telegram');
        }
        if ((time() - $auth_data['auth_date']) > 86400) {
            throw new Exception('Data is outdated');
        }

        dd($auth_data);

        return $auth_data;
    }
}
