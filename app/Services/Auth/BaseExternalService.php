<?php

namespace App\Services\Auth;

abstract class BaseExternalService
{
    const string URL_GET_TOKEN = '';
    const string URL_GET_USER_INFO = '';
    abstract protected function getToken(string $code) : string;
    abstract protected function getUserInfo(string $accessToken) : array;
}