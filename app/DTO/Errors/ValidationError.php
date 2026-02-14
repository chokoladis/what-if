<?php

namespace App\DTO\Errors;

readonly class ValidationError extends Error
{
    function __construct(
        string  $message,
        string  $field,
        ?string $code = 'validation_error',
    )
    {
        parent::__construct($message, $code);
    }

    public function __toString()
    {
        return $this->message.' [field: '.$this->field.']';
    }
}