<?php

namespace Salle\PixSalle\Model;

class Album {
    private String $title;
    private int $portID;

    public function __construct(string $title, int $portID) {
        $this->title = $title;
        $this->portID = $portID;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getPortID(): int
    {
        return $this->portID;
    }

    public function setPortID(int $portID): void
    {
        $this->portID = $portID;
    }



}
