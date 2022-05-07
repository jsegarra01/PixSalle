<?php

namespace Salle\PixSalle\Model;

class Picture {

    private String $url;
    private int $albumID;


 public function __construct(string $url, int $albumID) {
    $this->url = $url;
    $this->albumID = $albumID;
}

    public function getUrl(): string{
        return $this->url;
    }

    public function setUrl(string $url): void {
        $this->url = $url;
    }

    public function getAlbumID(): int {
        return $this->albumID;
    }

    public function setAlbumID(int $albumID): void {
        $this->albumID = $albumID;
    }





}