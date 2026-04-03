<?php

namespace App\Exceptions;

class SettingException extends \Exception
{
    protected $message = 'Set option failed';
    protected $code = 'set_option_failed';
}