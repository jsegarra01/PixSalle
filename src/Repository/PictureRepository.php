<?php

declare(strict_types=1);

namespace Salle\PixSalle\Repository;

use Salle\PixSalle\Model\Picture;

interface PictureRepository {
    public function uploadPicture(Picture $picture): void;
    public function getAllPictures(int $albumID);
    public function deletePicture(int $pictureID);
    public function deleteAllPictures(int $albumID);
    public function getAllPicturesUser();
}