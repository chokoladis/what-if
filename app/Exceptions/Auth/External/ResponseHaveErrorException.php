<?php

namespace App\Exceptions\Auth\External;

use Exception;

class ResponseHaveErrorException extends Exception
{
    public $code = 400;
    public $message = 'Response has error';
}