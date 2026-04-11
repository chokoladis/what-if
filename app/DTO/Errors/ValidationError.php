<?php

declare(strict_types=1);

namespace App\DTO\Errors;

readonly class ValidationError extends Error
{
    function __construct(
        public string $field,
        string        $message,
        string        $code = 'validation_error',
    )
    {
        parent::__construct($message, $code);
    }

    public function toArray(): array
    {
        return [
            'field' => $this->field,
            'message' => $this->message,
            'code' => $this->code,
        ];
    }
}