<?php

declare(strict_types=1);

namespace Salle\PixSalle\Service;

class ValidatorService
{
  public function __construct()
  {
  }

  public function validateEmail(string $email)
  {
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
      return 'The email address is not valid';
    } else if (!strpos($email, "@salle.url.edu")) {
      return 'Only emails from the domain @salle.url.edu are accepted.';
    }
    return '';
  }

  public function validatePassword(string $password)
  {
    if (empty($password) || strlen($password) < 6) {
      return 'The password must contain at least 6 characters.';
    } else if (!preg_match("~[0-9]+~", $password) || !preg_match("/[a-z]/", $password) || !preg_match("/[A-Z]/", $password)) {
      return 'The password must contain both upper and lower case letters and numbers';
    }
    return '';
  }

  public function validateUsername(string $username)
  {
    if (!empty($username) && ctype_alnum($username) == FALSE) {
      return 'Username must only contain alphanumeric characters';
    }
    return '';
  }

  public function validatePhone(string $phone)
  {
    if (!empty($phone)) {
      if(strlen($phone) != 9) {
        return 'Phone number must contain 9 digits';
      } else if (is_numeric($phone) == FALSE) {
        return 'Phone number must only contain numbers';
      } else if ( $phone[0] != 6 ) {
        return 'Phone numbers start with 6';
      }
    }
    return '';
  }

  public function validatePicture(int $picsize, string $imageFileType)
  {

    // Check file size
    if ($picsize > 1000000) {
      return "Sorry, your file is too large.";
    }

    // Allow certain file formats
    if($imageFileType != "jpg" && $imageFileType != "png") {
      return "Sorry, only JPG and PNG files are allowed.";
    }

    # TODO check file height width

    return '';
  }

  
}
