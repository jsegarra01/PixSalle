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

    public function isOwner(int $userID, int $albumID) {
        $query = <<<'QUERY'
        SELECT * 
        FROM album AS al 
        JOIN portfolio AS port ON al.port_id = port.id
        JOIN users AS u ON port.user_id = u.id
        WHERE al.id = :album_id AND u.id = :user_id
        QUERY;

        $statement = $this->databaseConnection->prepare($query);

        $statement->bindParam('user_id', $userID, PDO::PARAM_INT);

        $statement->bindParam('album_id', $albumID, PDO::PARAM_INT);

        $statement->execute();

        return $statement->fetch(PDO::FETCH_NAMED);
    }


    public function getAlbum(int $albumID) {
        $query = <<<'QUERY'
        SELECT name FROM album WHERE id = :album_id
        QUERY;

        $statement = $this->databaseConnection->prepare($query);

        $statement->bindParam('album_id', $albumID, PDO::PARAM_INT);

        $statement->execute();

        return $statement->fetch(PDO::FETCH_NAMED);
    }

    public function addQRAlbum(int $albumId, String $qrPath) {
        $query = <<<'QUERY'
        UPDATE album 
        SET qr_image = :qr_image 
        WHERE id = :album_id
        QUERY;

        $statement = $this->databaseConnection->prepare($query);

        $statement->bindParam('album_id', $albumId, PDO::PARAM_INT);
        $statement->bindParam('qr_image', $qrPath, PDO::PARAM_STR);

        $statement->execute();
    }

    public function getQRAlbum(int $albumId) {
        $query = <<<'QUERY'
        SELECT qr_image 
        FROM album 
        WHERE id = :album_id
        QUERY;

        $statement = $this->databaseConnection->prepare($query);

        $statement->bindParam('album_id', $albumId, PDO::PARAM_INT);

        $statement->execute();

        return $statement->fetch(PDO::FETCH_NAMED);
    }

    public function deleteAlbum(int $albumID) {
        $query = <<<'QUERY'
        DELETE FROM album WHERE id = :id
        QUERY;

        $statement = $this->databaseConnection->prepare($query);

        $statement->bindParam('id', $albumID, PDO::PARAM_INT);

        $statement->execute();
    }

}