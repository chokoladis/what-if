<?php

namespace App\DTO\Errors;

class ValidationError
{
    function __construct(
        string  $message,
        string  $field,
        ?string $code = 'validation_error',
    )
    {
    }
}