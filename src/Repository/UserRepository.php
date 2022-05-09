<?php

declare(strict_types=1);

namespace Salle\PixSalle\Repository;

use Salle\PixSalle\Model\User;

interface UserRepository
{
    public function createUser(User $user): void;
    public function editUser(User $user): void;
    public function getUserByEmail(string $email);
    public function getFunds(int $id);
    public function updateFunds(int $id, int $amount): int;
}
