<?php

declare(strict_types=1);

namespace Salle\PixSalle\Repository;

use PDO;
use Salle\PixSalle\Model\User;
use Salle\PixSalle\Repository\UserRepository;

final class MySQLUserRepository implements UserRepository
{
    private const DATE_FORMAT = 'Y-m-d H:i:s';

    private PDO $databaseConnection;

    public function __construct(PDO $database)
    {
        $this->databaseConnection = $database;
    }

    public function createUser(User $user): void
    {
        $query = <<<'QUERY'
        INSERT INTO users(email, password, createdAt, updatedAt, username, phone, picture, membership, funds)
        VALUES(:email, :password, :createdAt, :updatedAt, :username, :phone, :picture, :membership, :funds)
        QUERY;

        $statement = $this->databaseConnection->prepare($query);

        $email = $user->email();
        $password = $user->password();
        $createdAt = $user->createdAt()->format(self::DATE_FORMAT);
        $updatedAt = $user->updatedAt()->format(self::DATE_FORMAT);
        $username = $user->username();
        $phone = $user->phone();
        $picture = $user->picture();
        $membership = $user->membership();
        $funds = $user->funds();

        $statement->bindParam('email', $email, PDO::PARAM_STR);
        $statement->bindParam('password', $password, PDO::PARAM_STR);
        $statement->bindParam('createdAt', $createdAt, PDO::PARAM_STR);
        $statement->bindParam('updatedAt', $updatedAt, PDO::PARAM_STR);
        $statement->bindParam('username', $username, PDO::PARAM_STR);
        $statement->bindParam('phone', $phone, PDO::PARAM_STR);
        $statement->bindParam('picture', $picture, PDO::PARAM_STR);
        $statement->bindParam('membership', $membership, PDO::PARAM_STR);
        $statement->bindParam('funds', $funds, PDO::PARAM_INT);

        $statement->execute();
    }

    public function editUser(User $user): void
    {
        $query = <<<'QUERY'
        UPDATE users
        SET email=:email, password=:password, createdAt=:createdAt, updatedAt=:updatedAt, username=:username, phone=:phone, picture=:picture, membership=:membership
        WHERE email = :email
        QUERY;

        $statement = $this->databaseConnection->prepare($query);

        $email = $user->email();
        $password = $user->password();
        $createdAt = $user->createdAt()->format(self::DATE_FORMAT);
        $updatedAt = $user->updatedAt()->format(self::DATE_FORMAT);
        $username = $user->username();
        $phone = $user->phone();
        $picture = $user->picture();
        $membership = $user->membership();

        $statement->bindParam('email', $email, PDO::PARAM_STR);
        $statement->bindParam('password', $password, PDO::PARAM_STR);
        $statement->bindParam('createdAt', $createdAt, PDO::PARAM_STR);
        $statement->bindParam('updatedAt', $updatedAt, PDO::PARAM_STR);
        $statement->bindParam('username', $username, PDO::PARAM_STR);
        $statement->bindParam('phone', $phone, PDO::PARAM_STR);
        $statement->bindParam('picture', $picture, PDO::PARAM_STR);
        $statement->bindParam('membership', $membership, PDO::PARAM_STR);

        $statement->execute();
    }

    public function getUserByEmail(string $email)
    {
        $query = <<<'QUERY'
        SELECT * FROM users WHERE email = :email
        QUERY;

        $statement = $this->databaseConnection->prepare($query);

        $statement->bindParam('email', $email, PDO::PARAM_STR);

        $statement->execute();

        $count = $statement->rowCount();
        if ($count > 0) {
            $row = $statement->fetch(PDO::FETCH_OBJ);
            return $row;
        }
        return null;
    }

    public function getFunds(int $id)
    {
        $query = <<<'QUERY'
        SELECT * FROM users WHERE id = :id
        QUERY;

        $statement = $this->databaseConnection->prepare($query);

        $statement->bindParam('id', $id, PDO::PARAM_STR);

        $statement->execute();

        $count = $statement->rowCount();
        if ($count > 0) {
            $row = $statement->fetch(PDO::FETCH_OBJ);
            return $row;
        }
        return null;
    }

    public function updateFunds(int $id, int $amount): int
    {
        $user = $this->getFunds($id);

        $amount = $amount + $user->funds;

        $query = <<<'QUERY'
        UPDATE users
        SET funds=:funds
        WHERE id = :id
        QUERY;

        $statement = $this->databaseConnection->prepare($query);

        $statement->bindParam('id', $id, PDO::PARAM_STR);
        $statement->bindParam('funds', $amount, PDO::PARAM_STR);

        $statement->execute();

        return (int) $amount;

    }

    public function getUserAllPP() {
        $query = <<<'QUERY'
        SELECT username, picture FROM users
        QUERY;

        $statement = $this->databaseConnection->prepare($query);
        $statement->execute();

        $row = $statement->fetchAll(PDO::FETCH_NAMED);
        return $row;

    }
}
