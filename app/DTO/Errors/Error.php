<?php

declare(strict_types=1);

namespace App\DTO\Errors;

use App\Interfaces\DTO\ErrorInterface;

readonly class Error implements ErrorInterface
{
    function __construct(
        public string  $message,
        public string $code = 'system_error',
    )
    {
    }

    /**
     * @return string[]
     */
    public function toArray(): array
    {
        return [
            'message' => $this->message,
            'code' => $this->code
        ];
    }
}