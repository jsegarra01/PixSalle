<?php

namespace Salle\PixSalle\Repository;

use PDO;
use Salle\PixSalle\Model\Album;

class MySQLAlbumRepository {

    private PDO $databaseConnection;

    public function __construct(PDO $database) {
        $this->databaseConnection = $database;
    }

    public function createAlbum(Album $album): void {
        $query = <<<'QUERY'
        INSERT INTO album(name, port_id)
        VALUES(:title, :port_id)
        QUERY;

        $statement = $this->databaseConnection->prepare($query);

        $title = $album->getTitle();
        $portID = $album->getPortID();

        $statement->bindParam('title', $title, PDO::PARAM_STR);
        $statement->bindParam('port_id', $portID, PDO::PARAM_INT);

        $statement->execute();
    }

    public function getAllAlbums(int $portID) {
        $query = <<<'QUERY'
        SELECT * FROM album WHERE port_id = :port_id
        QUERY;

        $statement = $this->databaseConnection->prepare($query);

        $statement->bindParam('port_id', $portID, PDO::PARAM_INT);

        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_NAMED);
    }

}