<?php

namespace App\DTO\Errors;

readonly class Error implements \Stringable
{
    function __construct(
        protected string $message,
        protected ?string $code = 'system_error',
    )
    {
    }

    public function __toString()
    {
        return $this->message;
    }
}