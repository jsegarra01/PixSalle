<?php

declare(strict_types=1);

namespace Salle\PixSalle\Model;

use DateTime;

class User
{

  private int $id;
  private string $email;
  private string $password;
  private Datetime $createdAt;
  private Datetime $updatedAt;
  private string $username;
  private string $phone;
  private string $picture;
  private string $membership;
  private int $funds;

  public function __construct(
    string $email,
    string $password,
    Datetime $createdAt,
    Datetime $updatedAt,
    string $username = "",
    string $phone = "",
    string $picture = "https://media.forgecdn.net/avatars/107/154/636364134932167010.jpeg",
    string $membership = ""
  ) {
    $this->email = $email;
    $this->password = $password;
    $this->createdAt = $createdAt;
    $this->updatedAt = $updatedAt;
    $this->username = $username;
    $this->phone = $phone;
    $this->picture = $picture;
    $this->funds = 30;
    if ($membership=="") {
      $this->membership = "Cool";
    } else {
      $this->membership = $membership;
    }
  }

  public function id()
  {
    return $this->id;
  }

  public function setid(int $auxid)
  {
    $this->id = $auxid;
  }

  public function email()
  {
    return $this->email;
  }

  public function setemail(string $auxemail)
  {
    $this->email = $auxemail;
  }

  public function password()
  {
    return $this->password;
  }

  public function setpassword(string $auxpassword)
  {
    $this->password = $auxpassword;
  }

  public function createdAt()
  {
    return $this->createdAt;
  }

  public function setcreatedAt(DateTime $auxcreatedAt)
  {
    $this->createdAt = $auxcreatedAt;
  }

  public function updatedAt()
  {
    return $this->updatedAt;
  }

  public function setupdatedAt(DateTime $auxupdatedAt)
  {
    $this->updatedAt = $auxupdatedAt;
  }

  public function username()
  {
    return $this->username;
  }

  public function setusername(string $auxusername)
  {
    $this->username = $auxusername;
  }

  public function phone()
  {
    return $this->phone;
  }

  public function setphone(string $auxphone)
  {
    $this->phone = $auxphone;
  }

  public function picture()
  {
    return $this->picture;
  }

  public function setpicture(string $auxpicture)
  {
    $this->picture = $auxpicture;
  }

  public function membership()
  {
    return $this->membership;
  }

  public function setmembership(string $auxmembership)
  {
    $this->membership = $auxmembership;
  }

  public function funds():int
  {
      return $this->funds;
  }

  public function setFunds (int $funds): void
  {
      $this->funds = $funds;
  }

}
