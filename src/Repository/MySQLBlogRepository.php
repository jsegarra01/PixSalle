<?php

namespace Salle\PixSalle\Repository;

use PDO;

class MySQLBlogRepository implements BlogRepository
{
    private const DATE_FORMAT = 'Y-m-d H:i:s';

    private PDO $databaseConnection;

    public function __construct(PDO $database)
    {
        $this->databaseConnection = $database;
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

        $row = $statement->fetch(PDO::FETCH_NAMED);
        return $row;
    }

    public function deleteBlogById(int $id) {
        $query = <<<'QUERY'
        DELETE FROM blogs WHERE id = :id
        QUERY;

        $statement = $this->databaseConnection->prepare($query);
        $statement->bindParam('id', $id, PDO::PARAM_INT);
        $statement->execute();
    }

    public function postBlog(String $title, String $content, int $userId) {
        $query = <<<'QUERY'
        INSERT INTO blogs(title, content, userId)
        VALUES(:title, :content, :userId)
        QUERY;

        $statement = $this->databaseConnection->prepare($query);
        $statement->bindParam('userId', $userId, PDO::PARAM_INT);
        $statement->bindParam('content', $content, PDO::PARAM_STR);
        $statement->bindParam('title', $title, PDO::PARAM_STR);
        $statement->execute();

        $query = <<<'QUERY'
        SELECT * 
        FROM blogs 
        WHERE title = :title AND content = :content AND userId = :userId 
        GROUP BY id
        ORDER BY id ASC
        QUERY;

        $statement = $this->databaseConnection->prepare($query);
        $statement->bindParam('userId', $userId, PDO::PARAM_INT);
        $statement->bindParam('content', $content, PDO::PARAM_STR);
        $statement->bindParam('title', $title, PDO::PARAM_STR);
        $statement->execute();

        $row = $statement->fetch(PDO::FETCH_NAMED);
        return $row;
    }

    public function updateBlog(int $id, String $content, String $title)
    {
        $query = <<<'QUERY'
        UPDATE blogs
        SET title = :title, content = :content WHERE id = :id
        QUERY;

        $statement = $this->databaseConnection->prepare($query);
        $statement->bindParam('id', $id, PDO::PARAM_INT);
        $statement->bindParam('content', $content, PDO::PARAM_STR);
        $statement->bindParam('title', $title, PDO::PARAM_STR);
        $statement->execute();

        $query = <<<'QUERY'
        SELECT * FROM blogs WHERE id = :id
        QUERY;

        $statement = $this->databaseConnection->prepare($query);
        $statement->bindParam('id', $id, PDO::PARAM_STR);
        $statement->execute();

        $row = $statement->fetch(PDO::FETCH_NAMED);
        return $row;
    }
}