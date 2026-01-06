<?php

namespace App\DTO\Indexing;

use App\DTO\JsonDTO;
use App\Models\File;
use App\Models\User;

class UserDTO extends JsonDTO
{
    protected int $id;
    protected string $name;
    protected bool $active;
    protected ?File $photo;
    protected string $email;


    function __construct(
        User $user,
    )
    {
        $this->id = $user->id;
        $this->name = $user->name;
        $this->active = $user->active;
        $this->photo = $user->photo ?? null;
        $this->email = $user->email;
    }
}