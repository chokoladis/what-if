<?php

namespace App\Interfaces\Services;

use App\Models\File;

interface FileProxyRedisInterface
{
    public static function getFromRedis(?File $file, string $subdir);
}