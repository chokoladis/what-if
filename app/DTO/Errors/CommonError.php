<?php

namespace App\DTO\Errors;

class CommonError
{
    function __construct(
        string $message,
        ?string $code = 'system_error',
    )
    {}
}