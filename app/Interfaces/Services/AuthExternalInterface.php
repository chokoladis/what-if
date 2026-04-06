<?php

namespace App\Interfaces\Services;

use Illuminate\Http\RedirectResponse;

interface AuthExternalInterface
{
    public function setUser(array $userData): RedirectResponse|true;

    public function authorize(string $code): RedirectResponse|true;
}