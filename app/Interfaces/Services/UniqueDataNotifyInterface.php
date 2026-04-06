<?php

namespace App\Interfaces\Services;

interface UniqueDataNotifyInterface
{
    /**
     * @return array<string, int>
     */
    function getUniqueData(): array;
}
