<?php

namespace App\Library\Entity;

use App\Entity\User;

interface BlameableEntityInterface
{
    public function setBlamed(User $user): void;
}
