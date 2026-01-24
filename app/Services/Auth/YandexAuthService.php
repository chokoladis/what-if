<?php

namespace App\Services\Auth;

use App\Exceptions\Auth\External\IncorrectResponseException;
use App\Exceptions\Auth\External\ResponseHaveErrorException;
use App\Interfaces\Services\AuthExternalInterface;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

final class YandexAuthService extends BaseExternalService implements AuthExternalInterface
{
    const string URL_GET_TOKEN = 'https://oauth.yandex.ru/token';
    const string URL_GET_USER_INFO = 'https://login.yandex.ru/info';
    const YANDEX_LINK_PICTURE = 'https://avatars.mds.yandex.net/get-yapic/';

    protected function getToken(string $code) : string
    {
        try {
            $params = array(
                'client_id' => config('auth.socials.yandex.client_id'),
                'client_secret' => config('auth.socials.yandex.client_secret'),
                'grant_type' => 'authorization_code',
                'code' => $code
            );

            $ch = curl_init(self::URL_GET_TOKEN);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HEADER, false);
            $data = curl_exec($ch);
            curl_close($ch);

        } catch (\Throwable $th) {
            throw $th;
        }

        if (is_bool($data)){
            throw new IncorrectResponseException();
        }

        $data = json_decode($data, true);

        if (!empty($data['access_token'])) {
            return $data['access_token'];
        } elseif (!empty($data['error'])) {
            throw new ResponseHaveErrorException('Response has error: ' . $data['error']);
        } else {
            throw new IncorrectResponseException('Undefined response');
        }
    }

    protected function getUserInfo(string $accessToken) : array
    {
        $ch = curl_init(self::URL_GET_USER_INFO);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, array('format' => 'json'));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: OAuth ' . $accessToken));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $info = curl_exec($ch);
        curl_close($ch);

        $info = json_decode($info, true);

        if (is_bool($info)) {
            throw new IncorrectResponseException();
        }

        if (isset($info['error'])) {
            throw new ResponseHaveErrorException("Yandex API Error: " . ($info['error']['message'] ?? 'Unknown error'));
        }

        return $info;
    }

    public function setUser(array $userData)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($userData, [
            'default_email' => ['required', 'string', 'email'],
            'real_name' => ['required', 'string', 'max:150'],
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }
        //$userData["verified_email" => true]

        $validData = $validator->validated();

        $user = User::query()
            ->where('email', $validData['default_email'])
            ->first();

        if (!$user) {
            $imgLink = $imgLink = isset($userData['default_avatar_id']) && !empty($userData['default_avatar_id'])
                ? self::YANDEX_LINK_PICTURE . $userData['default_avatar_id']
                : null;

            $user = User::create([
                'name' => $validData['real_name'],
                'email' => $validData['default_email'],
                'password' => Str::random(12),
                'active' => 1,
                'profile_photo_path' => $imgLink,
            ]);

            // send psw on email
        }

        Auth::login($user);

        return true;
    }

    public function authorize(string $code)
    {
        try {
            $data = $this->getToken($code);

            $userData = $this->getUserInfo($data);

            return $this->setUser($userData);
        } catch (IncorrectResponseException|ResponseHaveErrorException $e){
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}