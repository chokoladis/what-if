<?php

namespace App\DTO\Errors;

readonly class CommonError extends Error
{
    function __construct(
        string  $message,
        ?string $code = 'system_error',
    )
    {
        parent::__construct($message, $code);
    }
}