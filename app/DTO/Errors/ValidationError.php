<?php

namespace App\DTO\Errors;

readonly class ValidationError
{
    function __construct(
        string  $message,
        string  $field,
        ?string $code = 'validation_error',
    )
    {
    }
}