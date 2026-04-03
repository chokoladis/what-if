<?php

declare(strict_types=1);

namespace App\Services\Auth;

use App\Repositories\UserRepository;

abstract class BaseExternalService
{
    const string URL_GET_TOKEN = '';
    const string URL_GET_USER_INFO = '';
    public UserRepository $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }

    abstract protected function getToken(string $code): string;

    abstract protected function getUserInfo(string $accessToken): array;
}