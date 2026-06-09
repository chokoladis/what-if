<?php

namespace App\Interfaces\AI;

use App\Models\File;
use App\Models\TempFile;

interface ValidatorAvatarContract
{
    //вместо моделей контракт?
    public function isContentFileLegal(TempFile|File $file): true;

    public function isSetOn(): bool;
}