<?php

namespace Salle\PixSalle\Model;

class Portfolio {
    private string $title;
    private int $userID;

    public function __construct(string $title, int $user_id) {
        $this->title = $title;
        $this->userID = $user_id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getUserID(): int
    {
        return $this->userID;
    }

    public function setUserID(int $userID): void
    {
        $this->userID = $userID;
    }


}