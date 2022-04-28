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

  public function __construct(
    string $email,
    string $password,
    Datetime $createdAt,
    Datetime $updatedAt,
    string $username = "",
    string $phone = "",
    string $picture = ""
  ) {
    $this->email = $email;
    $this->password = $password;
    $this->createdAt = $createdAt;
    $this->updatedAt = $updatedAt;
    $this->username = $username;
    $this->phone = $phone;
    $this->picture = $picture;
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

}
