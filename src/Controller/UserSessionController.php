<?php
declare(strict_types=1);

namespace Salle\PixSalle\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Salle\PixSalle\Service\ValidatorService;
use Salle\PixSalle\Repository\UserRepository;
use Salle\PixSalle\Model\User;
use Slim\Flash\Messages;
use Slim\Views\Twig;
use Slim\Routing\RouteContext;

class UserSessionController
{
    private Twig $twig;
    private Messages $flash;
    private ValidatorService $validator;
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

    public function showSignInForm(Request $request, Response $response): Response {
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

        if( isset($_SESSION['user_id']) ) {
            unset($_SESSION['user_id']);
        }
        if( isset($_SESSION['email']) ) {
            unset($_SESSION['email']);
        }

        $messages = $this->flash->getMessages();

        $signError = $messages['signError'][0] ?? "";

        return $this->twig->render(
            $response, 
            'sign-in.twig',
            [
                'currentPage' => ['sign-in'],
                'notSigned' => '1',
                'signError' => $signError,
                'formAction' => $routeParser->urlFor('signIn')
            ]);
    }

    public function signIn(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

        $errors = [];

        $errors['email'] = $this->validator->validateEmail($data['email']);
        $errors['password'] = $this->validator->validatePassword($data['password']);

        if ($errors['email'] == '') {
            unset($errors['email']);
        }
        if ($errors['password'] == '') {
            unset($errors['password']);
        }
        if (count($errors) == 0) {
            // Check if the credentials match the user information saved in the database
            $user = $this->userRepository->getUserByEmail($data['email']);
            if ($user == null) {
                $errors['email'] = 'User with this email address does not exist.';
            } else if ($user->password != md5($data['password'])) {
                $errors['password'] = 'Your email and/or password are incorrect.';
            } else {
                $_SESSION['user_id'] = $user->id;
                $_SESSION['email'] = $data['email'];
                $_SESSION['username'] = $user->username;
                return $response->withHeader('Location','/')->withStatus(302);
            }
        }
        return $this->twig->render(
            $response,
            'sign-in.twig',
            [
                'currentPage' => ['sign-in'],
                'notSigned' => '1',
                'formErrors' => $errors,
                'formData' => $data,
                'formAction' => $routeParser->urlFor('signIn')
            ]
        );
    }
}