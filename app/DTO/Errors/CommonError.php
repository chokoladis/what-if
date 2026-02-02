<?php

namespace App\DTO\Errors;

readonly class CommonError
{
    function __construct(
        string  $message,
        ?string $code = 'system_error',
    )
    {
    }
}