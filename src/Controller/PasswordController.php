<?php
declare(strict_types=1);

namespace Salle\PixSalle\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Salle\PixSalle\Repository\UserRepository;
use Salle\PixSalle\Service\ValidatorService;
use Salle\PixSalle\Model\User;
use Slim\Flash\Messages;
use Slim\Views\Twig;
use Slim\Routing\RouteContext;

class PasswordController
{
    private Twig $twig;
    private Messages $flash;
    private UserRepository $userRepository;

    public function __construct(
        Twig $twig,
        Messages $flash,
        UserRepository $userRepository
    ) {
        $this->twig = $twig;
        $this->flash = $flash;
        $this->userRepository = $userRepository;
        $this->validator = new ValidatorService();
    }

    public function showChangePassword(Request $request, Response $response): Response {

        if (!isset($_SESSION["user_id"])) {
            $this->flash->addMessage(
                'signError',
                'You have to be logged in to access the PASSWORD EDIT!'
            );
            $routeParser = RouteContext::fromRequest($request)->getRouteParser();
            return $response
                ->withHeader('Location', $routeParser->urlFor("signIn"))
                ->withStatus(302);
        }

        $user = $this->userRepository->getUserByEmail($_SESSION['email']);

        $data = [];
        $data['username'] = $user->username;

        $data['uuid'] = $user->picture;
        if (str_contains($user->picture, "uploads/")) {
            $data['picture'] = '../' . $user->picture;
        } else {
            $data['picture'] = $user->picture;
        }

        
        return $this->twig->render($response,'editPassword.twig',
            [
                'currentPage' => ['profile', 'password'],
                'formData' => $data
            ]
        );
    }

    public function changePassword(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

        $errors = [];
        $errors['oldpassword'] = $this->validator->validatePassword($data['oldpassword']);
        $errors['newpassword'] = $this->validator->validatePassword($data['newpassword']);
        $errors['confirmnewpassword'] = $this->validator->validatePassword($data['confirmnewpassword']);

        if ($errors['oldpassword'] == '') {
            unset($errors['oldpassword']);
        }
        if ($errors['newpassword'] == '') {
            unset($errors['newpassword']);
        }
        if ($errors['confirmnewpassword'] == '') {
            unset($errors['confirmnewpassword']);
        }
        
        $userdata = $this->userRepository->getUserByEmail($_SESSION['email']);


        $data['username'] = $userdata->username;
        $data['uuid'] = $userdata->picture;
        $data['picture'] = '../uploads/' . $userdata->picture;

        $createdAt = date_create_from_format('Y-m-d H:i:s', $userdata->createdAt);
        $updatedAt = date_create_from_format('Y-m-d H:i:s', $userdata->updatedAt);

        $user = new User($userdata->email, $userdata->password, $createdAt, $updatedAt, $userdata->username, $userdata->phone, $userdata->picture);

        $errors['verification'] = 'Old password does not match';
        $errors['equals'] = 'New password does not match';

        if( $userdata->password == md5( $data['oldpassword'] ) ) {
            unset($errors['verification']);
        }

        if ( $data['confirmnewpassword'] == $data['newpassword'] ) {
            unset($errors['equals']);
        }

        if (count($errors) == 0) {
            $user->setpassword( md5($data['newpassword']) );
            $this->userRepository->editUser($user);
            return $response->withHeader('Location', '/profile')->withStatus(302);
        }

        return $this->twig->render(
            $response,
            'editPassword.twig',
            [
                'currentPage' => ['profile', 'password'],
                'formErrors' => $errors,
                'formData' => $data,
                'formAction' => $routeParser->urlFor('changePassword')
            ]
        );
    }

}