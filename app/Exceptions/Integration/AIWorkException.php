<?php

namespace App\Exceptions\Integration;

use Exception;

class AIWorkException extends Exception
{
    protected $message = 'Error work with AI';
}