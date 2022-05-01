<?php
declare(strict_types=1);

namespace Salle\PixSalle\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Salle\PixSalle\Repository\UserRepository;
use Salle\PixSalle\Service\ValidatorService;
use Salle\PixSalle\Model\User;
use Slim\Views\Twig;
use Slim\Routing\RouteContext;

class ProfileController
{
    private Twig $twig;
    private UserRepository $userRepository;

    public function __construct(
        Twig $twig,
        UserRepository $userRepository
    ) {
        $this->twig = $twig;
        $this->userRepository = $userRepository;
        $this->validator = new ValidatorService();
    }

    public function showProfile(Request $request, Response $response): Response {

        if (!isset($_SESSION["user_id"])) {
            $routeParser = RouteContext::fromRequest($request)->getRouteParser();
            return $response
                ->withHeader('Location', $routeParser->urlFor("signIn"))
                ->withStatus(302);
        }

        $user = $this->userRepository->getUserByEmail($_SESSION['email']);

        $data = [];
        if( $user->username!="" ) $data['username'] = $user->username;
        else $data['username'] = "Not set";

        if( $user->phone!="" ) $data['phone'] = $user->phone;
        else $data['phone'] = "Not set";

        if( $user->picture!="" ) {
            $data['uuid'] = $user->picture;
            $data['picture'] = 'uploads/' . $user->picture;
        }
        else $data['uuid'] = "No picture yet";

        $data['email'] = $user->email;
        
        return $this->twig->render(
            $response,
            'profile.twig',
            [
                'formData' => $data,
            ]
        );
    }

    public function editProfile(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();
        
        $errors = [];
        $errors['username'] = $this->validator->validateUsername($data['username']);
        $errors['phone'] = $this->validator->validatePhone($data['phone']);
        $errors['picture'] = '';

        $newPic = FALSE;
        $target_dir = "uploads/";
        
        if ( $_FILES['pic']['name'] != '' ) {
            
            $target_file = $target_dir . basename($_FILES['pic']['name']);
            $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
            $uuid = uniqid("pic", false) . '.' . $imageFileType;
            $target_file = $target_dir . $uuid;

            // Check if image file is a actual image or fake image
            $check = getimagesize($_FILES["pic"]["tmp_name"]);
            if($check !== false) {
                $errors['picture'] = $this->validator->validatePicture($_FILES["pic"]["size"], $imageFileType, $check[0], $check[1]);
                if ($errors['picture'] == '') {
                    if(!move_uploaded_file($_FILES["pic"]["tmp_name"], $target_file)) {
                        $errors['picture'] = 'File could not be uploaded';
                    } else {
                        $newPic = TRUE;
                    }
                }
            } else {
                $errors['picture'] = "File is not an image.";
            }

        }

        if ($errors['picture'] == '') {
            unset($errors['picture']);
        }
        if ($errors['username'] == '') {
            unset($errors['username']);
        }
        if ($errors['phone'] == '') {
            unset($errors['phone']);
        }
        
        $userdata = $this->userRepository->getUserByEmail($_SESSION['email']);
        $createdAt = date_create_from_format('Y-m-d H:i:s', $userdata->createdAt);
        $updatedAt = date_create_from_format('Y-m-d H:i:s', $userdata->updatedAt);

        $user = new User($userdata->email, $userdata->password, $createdAt, $updatedAt, $userdata->username, $userdata->phone, $userdata->picture);

        if (count($errors) == 0) {
            if($data['username']!="") {$user->setusername($data['username']);}
            if($data['phone']!="") {$user->setphone($data['phone']);}
            if( $newPic == TRUE ) {$user->setpicture($uuid);}
            $this->userRepository->editUser($user);
            return $response->withHeader('Location', '/profile')->withStatus(302);
        }
        
        $data = [];
        if( $userdata->username!="" ) $data['username'] = $userdata->username;
        else $data['username'] = "Not set";

        if( $userdata->phone!="" ) $data['phone'] = $userdata->phone;
        else $data['phone'] = "Not set";

        if( $userdata->picture!="" ) {
            $data['uuid'] = $userdata->picture;
            $data['picture'] = 'uploads/' . $userdata->picture;
        }
        else $data['uuid'] = "No picture yet";

        $data['email'] = $userdata->email;

        return $this->twig->render(
            $response,
            'profile.twig',
            [
                'formErrors' => $errors,
                'formData' => $data,
                'formAction' => $routeParser->urlFor('profile')
            ]
        );
    }

}