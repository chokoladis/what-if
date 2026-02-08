<?php

namespace App\Enums;

enum Vote:int
{
    case LIKE = 1;
    case DISLIKE = -1;

    public function isLike()
    {
        return $this === self::LIKE;
    }
}
