<?php

namespace App\Exceptions;

class FileSaveException extends \Exception
{
    protected $message = 'Ошибка сохранения файла';
    protected $code = 'file_save_error';
}