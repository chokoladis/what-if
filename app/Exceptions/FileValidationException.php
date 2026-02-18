<?php

namespace App\Exceptions;

class FileValidationException extends \Exception
{
    protected $message = 'File validation failed';
    protected $code = 'file_validation';
}