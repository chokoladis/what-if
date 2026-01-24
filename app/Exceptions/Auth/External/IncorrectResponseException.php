<?php

namespace App\Exceptions\Auth\External;

class IncorrectResponseException extends \Exception
{
    public $code = 400;
    public $message = 'Incorrect response for request token';
}