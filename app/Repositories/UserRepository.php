<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Str;

class UserRepository
{
    /**
     * @param array<string, string|int> $data
     * @return User
     */
    public function create(array $data)
    {
        if (empty($data['password'])) {
            $data['password'] = Str::random(12);
        }

        return User::create($data);
    }

    /**
     * @param array<string, string|int> $data
     * @return User
     */
    public function createIfNotExists(array $data)
    {
        if (empty($data['password'])) {
            $data['password'] = Str::random(12);
        }

        // send psw on email

        return User::firstOrCreate(['email' => $data['email']], $data);
    }
}