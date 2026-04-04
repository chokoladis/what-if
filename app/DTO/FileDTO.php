<?php

namespace App\DTO;

//use Illuminate\Filesystem\Filesystem;

readonly class FileDTO
{
    function __construct(
        public string $name,
        public string $ext,
        public string $filePath,
        public string $mainDir
    )
    {

    }
}