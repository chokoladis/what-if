<?php

namespace App\Interfaces\AI;


use App\Exceptions\Integration\AIWorkException;
use GuzzleHttp\Exception\ConnectException;

interface AIClientContract
{
    public function isSetOn(): bool;

    /**
     * @param mixed $data
     * @return mixed
     * @throws AIWorkException
     * @throws ConnectException
     */
    public function sendRequest(mixed $data): mixed;
}
