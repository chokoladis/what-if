<?php

namespace App\Exceptions;

use Exception;

class SettingException extends Exception
{
    protected $message = 'Set option failed';
    protected $code = 'set_option_failed';
}