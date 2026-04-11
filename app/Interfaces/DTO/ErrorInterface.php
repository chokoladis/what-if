<?php

namespace App\Interfaces\DTO;

interface ErrorInterface
{
    /** @return array<string, string> */
    public function toArray(): array;
}