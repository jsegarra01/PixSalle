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
        INSERT INTO users(email, password, createdAt, updatedAt, username, phone, picture, membership)
        VALUES(:email, :password, :createdAt, :updatedAt, :username, :phone, :picture, :membership)
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

    public function getUserAllPP() {
        $query = <<<'QUERY'
        SELECT username, picture FROM users
        QUERY;

        $statement = $this->databaseConnection->prepare($query);
        $statement->execute();

        $row = $statement->fetchAll(PDO::FETCH_NAMED);
        return $row;

    }

    public function getUserAllBlogs() {
        $query = <<<'QUERY'
        SELECT * FROM blogs
        QUERY;

        $statement = $this->databaseConnection->prepare($query);
        $statement->execute();

        $row = $statement->fetchAll(PDO::FETCH_NAMED);
        return $row;

    }

    public function getBlogById(int $id) {
        $query = <<<'QUERY'
        SELECT * FROM blogs WHERE id = :id
        QUERY;

        $statement = $this->databaseConnection->prepare($query);
        $statement->bindParam('id', $id, PDO::PARAM_STR);
        $statement->execute();

        $row = $statement->fetchAll(PDO::FETCH_NAMED);
        return $row;
    }

    public function deleteBlogById(int $id) {
        $query = <<<'QUERY'
        DELETE FROM blogs WHERE id = :id
        QUERY;

        $statement = $this->databaseConnection->prepare($query);
        $statement->bindParam('id', $id, PDO::PARAM_STR);
        $statement->execute();
    }

    public function postBlog(String $title, String $content, int $user_id) {
        $query = <<<'QUERY'
        INSERT INTO blogs(title, content, user_id)
        VALUES(:title, :content, :user_id)
        QUERY;

        $statement = $this->databaseConnection->prepare($query);
        $statement->bindParam('user_id', $user_id, PDO::PARAM_STR);
        $statement->bindParam('content', $content, PDO::PARAM_STR);
        $statement->bindParam('title', $title, PDO::PARAM_STR);
        $statement->execute();

        $query = <<<'QUERY'
        SELECT * FROM blogs WHERE title = :title AND content = :content AND user_id = :user_id
        QUERY;

        $statement->bindParam('user_id', $user_id, PDO::PARAM_STR);
        $statement->bindParam('content', $content, PDO::PARAM_STR);
        $statement->bindParam('title', $title, PDO::PARAM_STR);
        $statement->execute();

        $row = $statement->fetchAll(PDO::FETCH_NAMED);
        return $row;
    }

    public function updateBlog(int $id, String $content, String $title)
    {
        $query = <<<'QUERY'
        UPDATE blogs
        SET title = :title AND content = :content WHERE id = :id
        QUERY;

        $statement = $this->databaseConnection->prepare($query);
        $statement->bindParam('id', $id, PDO::PARAM_STR);
        $statement->bindParam('content', $content, PDO::PARAM_STR);
        $statement->bindParam('title', $title, PDO::PARAM_STR);
        $statement->execute();

        $query = <<<'QUERY'
        SELECT * FROM blogs WHERE id = :id
        QUERY;

        $statement->bindParam('id', $id, PDO::PARAM_STR);
        $statement->execute();

        $row = $statement->fetchAll(PDO::FETCH_NAMED);
        return $row;
    }
}
