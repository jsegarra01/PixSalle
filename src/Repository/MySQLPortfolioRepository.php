<?php

namespace Salle\PixSalle\Repository;

use PDO;
use Salle\PixSalle\Model\Portfolio;

class MySQLPortfolioRepository {

    private PDO $databaseConnection;

    public function __construct(PDO $database) {
        $this->databaseConnection = $database;
    }

    public function createPortfolio(Portfolio $portfolio): void {
        $query = <<<'QUERY'
        INSERT INTO portfolio(title, user_id)
        VALUES(:title, :user_id)
        QUERY;

        $statement = $this->databaseConnection->prepare($query);

        $title = $portfolio->getTitle();
        $userID = $portfolio->getUserID();

        $statement->bindParam('title', $title, PDO::PARAM_STR);
        $statement->bindParam('user_id', $userID, PDO::PARAM_INT);

        $statement->execute();
    }

    public function getUserPortfolio(int $use_id) {
        $query = <<<'QUERY'
        SELECT * FROM portfolio WHERE user_id = :user_id
        QUERY;

        $statement = $this->databaseConnection->prepare($query);

        $statement->bindParam('user_id', $use_id, PDO::PARAM_INT);

        $statement->execute();

        return $statement->fetch(PDO::FETCH_NAMED);
    }
}