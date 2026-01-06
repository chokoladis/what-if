<?php

namespace App\DTO\Indexing;

use App\DTO\JsonDTO;

class CommentDTO extends JsonDTO
{
    public function __construct(
        protected int $id,
        protected string $text,
        protected UserDTO $user,
    )
    {
    }
}