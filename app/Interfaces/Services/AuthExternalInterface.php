<?php

namespace App\Interfaces\Services;

use Illuminate\Http\RedirectResponse;

interface AuthExternalInterface
{
    /**
     * @param array<string, string|int> $userData
     * @return RedirectResponse|true
     */
    public function setUser(array $userData): RedirectResponse|true;

    public function authorize(string $code): RedirectResponse|true;
}