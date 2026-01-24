<?php

namespace App\Services\Auth;

use App\Exceptions\Auth\External\IncorrectResponseException;
use App\Exceptions\Auth\External\ResponseHaveErrorException;
use App\Interfaces\Services\AuthExternalInterface;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

final class GoogleAuthService extends BaseExternalService implements AuthExternalInterface
{
    const string URL_GET_TOKEN = 'https://accounts.google.com/o/oauth2/token';
    const string URL_GET_USER_INFO = 'https://www.googleapis.com/oauth2/v1/userinfo';

    protected function getToken(string $code) : string
    {
        try {
            $params = array(
                'client_id' => config('auth.socials.google.client_id'),
                'client_secret' => config('auth.socials.google.client_secret'),
                'redirect_uri' => config('auth.socials.google.redirect_uri'),
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
        } elseif (isset($data['error']) && !empty($data['error'])) {
            throw new ResponseHaveErrorException('Response has error: ' . $data['error']);
        } else {
            throw new IncorrectResponseException('Undefined response');
        }
    }

    protected function getUserInfo(string $accessToken) : array
    {
        $options = [
            "ssl" => [
                "crypto_method" => STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT,
                "verify_peer" => true,
                "verify_peer_name" => true,
            ],
            "http" => [
                "ignore_errors" => true,
                "timeout" => 10
            ]
        ];

        $params = ['access_token' => $accessToken];

        $response = file_get_contents(
            self::URL_GET_USER_INFO.'?' . http_build_query($params),
            false,
            stream_context_create($options)
        );

        if ($response === false) {
            $error = error_get_last();
            throw new \Exception("Connection failed: " . $error['message']);
        }
        $info = json_decode($response, true);

        if (isset($info['error'])) {
            throw new ResponseHaveErrorException("Google API Error: " . ($info['error']['message'] ?? 'Unknown error'));
        }

        return $info;
    }

    public function setUser(array $userData)
    {
        $validator = Validator::make($userData, [
            'email' => ['required', 'string', 'email'],
            'name' => ['required', 'string', 'max:150'],
            'picture' => ['url', 'nullable'], //todo check img
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }
        //$userData["verified_email" => true]

        $validData = $validator->validated();

        $user = User::query()
            ->where('email', $validData['email'])
            ->first();

        if (!$user) {
            $user = User::create([
                'name' => $validData['name'],
                'email' => $validData['email'],
                'password' => Str::random(12),
                'active' => 1,
                'profile_photo_path' => $validData['picture'],
            ]);

            // send psw on email
        }

        Auth::login($user);

        return true;
    }

    public function authorize(string $code)
    {
        try {
            $userData = $this->getUserInfo($this->getToken($code));

            return $this->setUser($userData);
        } catch (IncorrectResponseException|ResponseHaveErrorException $e){
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}