<?php

declare(strict_types=1);

namespace App\Services\Auth;

use App\Exceptions\Auth\External\IncorrectResponseException;
use App\Exceptions\Auth\External\ResponseHaveErrorException;
use App\Interfaces\Services\AuthExternalInterface;
use Exception;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

final class GoogleAuthService extends BaseExternalService implements AuthExternalInterface
{
    const string URL_GET_TOKEN = 'https://accounts.google.com/o/oauth2/token';
    const string URL_GET_USER_INFO = 'https://www.googleapis.com/oauth2/v1/userinfo';

    public function authorize(string $code): RedirectResponse|true
    {
        try {
            $userData = $this->getUserInfo($this->getToken($code));

            return $this->setUser($userData);
        } catch (IncorrectResponseException|ResponseHaveErrorException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * @param string $accessToken
     * @throws IncorrectResponseException
     * @throws ResponseHaveErrorException
     * @throws ConnectionException
     */
    protected function getUserInfo(string $accessToken): array
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
            self::URL_GET_USER_INFO . '?' . http_build_query($params),
            false,
            stream_context_create($options)
        );

        if ($response === false) {
            $error = error_get_last();
            throw new Exception("Connection failed: " . (!empty($error['message']) ? $error['message'] : 'undefined'));
        }

        $info = json_decode($response, true);

        if (isset($info['error'])) {
            throw new ResponseHaveErrorException("Google API Error: " . ($info['error']['message'] ?? 'Unknown error'));
        }

        return $info;
    }

    protected function getToken(string $code): string
    {
        $response = Http::withoutVerifying()
            ->asForm()
            ->post(self::URL_GET_TOKEN, [
                'client_id' => config('auth.socials.google.client_id'),
                'client_secret' => config('auth.socials.google.client_secret'),
                'redirect_uri' => config('auth.socials.google.redirect_uri'),
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

        throw new ResponseHaveErrorException('Response has error: ' . $response->json('error'));
    }

    public function setUser(array $userData): RedirectResponse|true
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

        $user = $this->userRepository->createIfNotExists([
            'name' => $validData['name'],
            'email' => $validData['email'],
            'photo_url' => $validData['picture'],
            'active' => 1,
        ]);

        Auth::login($user);

        return true;
    }
}