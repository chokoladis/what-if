<?php

namespace App\Exceptions\Integration;

use Exception;

class InvalidCaptchaException extends Exception
{
    protected $message = (string)__('services.integrations.invalid_captcha');
}
