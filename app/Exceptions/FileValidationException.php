<?php

namespace App\Exceptions;

use Exception;

class FileValidationException extends Exception
{
    protected $message = 'File validation failed';
    protected $code = 'file_validation';
}