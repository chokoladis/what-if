<?php

namespace App\Exceptions\Integration;

use Exception;

class InvalidCaptchaException extends Exception
{
    protected $message = 'invalid_captcha';
}
