<?php

namespace Salle\PixSalle\Repository;

use PDO;
use Salle\PixSalle\Model\Picture;

class MySQLPictureRepository implements PictureRepository{

    private PDO $databaseConnection;

    public function __construct(PDO $database) {
        $this->databaseConnection = $database;
    }

    public function uploadPicture(Picture $picture): void {
        $query = <<<'QUERY'
        INSERT INTO picture(pic_url, album_id)
        VALUES(:url, :album_id)
        QUERY;

        $statement = $this->databaseConnection->prepare($query);

        $picURL = $picture->getUrl();
        $albumID = $picture->getAlbumID();

        $statement->bindParam('url', $picURL, PDO::PARAM_STR);
        $statement->bindParam('album_id', $albumID, PDO::PARAM_INT);

        $statement->execute();
    }

    public function getAllPictures(int $albumID) {
        $query = <<<'QUERY'
        SELECT * FROM picture WHERE album_id = :album_id
        QUERY;

        $statement = $this->databaseConnection->prepare($query);

        $statement->bindParam('album_id', $albumID, PDO::PARAM_INT);

        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_NAMED);
    }

    public function deletePicture(int $pictureID) {
        $query = <<<'QUERY'
        DELETE FROM picture WHERE id = :id
        QUERY;

        $statement = $this->databaseConnection->prepare($query);

        $statement->bindParam('id', $pictureID, PDO::PARAM_INT);

        $statement->execute();
    }

    public function deleteAllPictures(int $albumID) {
        $query = <<<'QUERY'
        DELETE FROM picture WHERE album_id = :album_id
        QUERY;

        $statement = $this->databaseConnection->prepare($query);

        $statement->bindParam('album_id', $albumID, PDO::PARAM_INT);

        $statement->execute();
    }

    public function getAllPicturesUser() {
        $query = <<<'QUERY'
        SELECT pic.pic_url, username
        FROM picture AS pic
        JOIN album AS al ON pic.album_id = al.id
        JOIN portfolio AS port ON al.port_id = port.id
        JOIN users AS u ON port.user_id = u.id
        QUERY;

        $statement = $this->databaseConnection->prepare($query);

        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_NAMED);
    }
}