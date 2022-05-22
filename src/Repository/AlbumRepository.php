<?php

declare(strict_types=1);

namespace Salle\PixSalle\Repository;

use Salle\PixSalle\Model\Album;

interface AlbumRepository {
    public function createAlbum(Album $album): void;
    public function getAllAlbums(int $portID);
    public function isOwner(int $userID, int $albumID);
    public function getAlbum(int $albumID);
    public function addQRAlbum(int $albumId, String $qrPath);
    public function getQRAlbum(int $albumId);
    public function deleteAlbum(int $albumID);
}