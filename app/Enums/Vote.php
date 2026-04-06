<?php

namespace App\Enums;

enum Vote: int
{
    case LIKE = 1;
    case DISLIKE = -1;

    public function isLike(): bool
    {
        return $this === self::LIKE;
    }
}
