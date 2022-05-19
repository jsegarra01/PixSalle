<?php

namespace Salle\PixSalle\Repository;

class MySQLBlogRepository
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

        $row = $statement->fetchAll(PDO::FETCH_NAMED);
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

    public function postBlog(String $title, String $content, int $user_id) {
        $query = <<<'QUERY'
        INSERT INTO blogs(title, content, user_id)
        VALUES(:title, :content, :user_id)
        QUERY;

        $statement = $this->databaseConnection->prepare($query);
        $statement->bindParam('user_id', $user_id, PDO::PARAM_INT);
        $statement->bindParam('content', $content, PDO::PARAM_STR);
        $statement->bindParam('title', $title, PDO::PARAM_STR);
        $statement->execute();

        $query = <<<'QUERY'
        SELECT * FROM blogs WHERE title = :title AND content = :content AND user_id = :user_id
        QUERY;

        $statement = $this->databaseConnection->prepare($query);
        $statement->bindParam('user_id', $user_id, PDO::PARAM_INT);
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

        $row = $statement->fetchAll(PDO::FETCH_NAMED);
        return $row;
    }
}