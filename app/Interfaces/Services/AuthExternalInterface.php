<?php

namespace App\Interfaces\Services;

interface AuthExternalInterface
{
    public function setUser(array $userData);
    public function authorize(string $code);
}