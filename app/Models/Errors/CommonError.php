<?php

namespace App\Models\Errors;

class CommonError
{
    public string $message;
    public ?string $code = null;
    public ?string $field = null;

    public function __construct(
        string $message, string $code = null, string $field = null
    )
    {
        $this->message = $message;
        $this->code = $code ?? 'system_error';
        $this->field = $field;
    }
}
