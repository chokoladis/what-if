<?php

declare(strict_types=1);

namespace App\Services\Auth;

use App\Exceptions\Auth\External\IncorrectResponseException;
use App\Exceptions\Auth\External\ResponseHaveErrorException;
use App\Interfaces\Services\AuthExternalInterface;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

final class YandexAuthService extends BaseExternalService implements AuthExternalInterface
{
    const string URL_GET_TOKEN = 'https://oauth.yandex.ru/token';
    const string URL_GET_USER_INFO = 'https://login.yandex.ru/info';
    const string YANDEX_LINK_PICTURE = 'https://avatars.mds.yandex.net/get-yapic/';

    public function authorize(string $code)
    {
        try {
            return $this->setUser(
                $this->getUserInfo(
                    $this->getToken($code)
                )
            );
        } catch (IncorrectResponseException|ResponseHaveErrorException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function setUser(array $userData)
    {
        $validator = Validator::make($userData, [
            'default_email' => ['required', 'string', 'email'],
            'real_name' => ['required', 'string', 'max:150'],
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }
        //$userData["verified_email" => true]

        $validData = $validator->validated();

        $user = $this->userRepository->createIfNotExists([
            'name' => $validData['real_name'],
            'email' => $validData['default_email'],
            'active' => 1,
            'profile_photo_path' => !empty($userData['default_avatar_id'])
                ? self::YANDEX_LINK_PICTURE . $userData['default_avatar_id'] : null,
        ]);

        Auth::login($user);

        return true;
    }

    protected function getUserInfo(string $accessToken): array
    {
        $response = Http::withoutVerifying()
            ->asForm()
            ->withHeader('Authorization', 'OAuth ' . $accessToken)
            ->post(self::URL_GET_USER_INFO);

        try {
            $response->throw();
        } catch (RequestException $e) {
            throw new IncorrectResponseException('HTTP ' . $e->response->status() . ': ' . $e->response->body());
        }

        $info = $response->json();

        if (isset($info['error'])) {
            throw new ResponseHaveErrorException("Yandex API Error: " . ($info['error']['message'] ?? 'Unknown error'));
        }

        return $info;
    }

    protected function getToken(string $code): string
    {
        $response = Http::withoutVerifying()
            ->asForm()
            ->post(self::URL_GET_TOKEN, [
                'client_id' => config('auth.socials.yandex.client_id'),
                'client_secret' => config('auth.socials.yandex.client_secret'),
                'grant_type' => 'authorization_code',
                'code' => $code
            ]);

        try {
            $response->throw();
        } catch (RequestException $e) {
            throw new IncorrectResponseException('HTTP ' . $e->response->status() . ': ' . $e->response->body());
        }

        if (!empty($response->json('access_token'))) {
            return (string)$response->json('access_token');
        }

        throw new ResponseHaveErrorException('Response has error: ' . $response->json('error') ?? 'undefined');
    }
}