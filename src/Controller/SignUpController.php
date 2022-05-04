<?php

declare(strict_types=1);

namespace Salle\PixSalle\Controller;

use Salle\PixSalle\Service\ValidatorService;
use Salle\PixSalle\Repository\UserRepository;
use Salle\PixSalle\Model\User;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Slim\Routing\RouteContext;
use Slim\Views\Twig;

use DateTime;

final class SignUpController
{
    private Twig $twig;
    private ValidatorService $validator;
    private UserRepository $userRepository;

    public function __construct(
        Twig $twig,
        UserRepository $userRepository
    ) {
        $this->twig = $twig;
        $this->userRepository = $userRepository;
        $this->validator = new ValidatorService();
    }

    /**
     * Renders the form
     */
    public function showSignUpForm(Request $request, Response $response): Response
    {
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

        if( isset($_SESSION['user_id']) ) {
            unset($_SESSION['user_id']);
        }
        if( isset($_SESSION['email']) ) {
            unset($_SESSION['email']);
        }

        return $this->twig->render(
            $response,
            'sign-up.twig',
            [
                'currentPage' => ['sign-up'],
                'notSigned' => '1',
                'formAction' => $routeParser->urlFor('signUp')
            ]
        );
    }

    public function signUp(Request $request, Response $response): Response
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
        
        $savedUser = $this->userRepository->getUserByEmail($data['email']);
        if ($savedUser != null) {
            $errors['email'] = "User already exists!";
        }
        if (count($errors) == 0) {
            $user = new User($data['email'], md5($data['password']), new DateTime(), new DateTime());
            $this->userRepository->createUser($user);
            $savedUser = $this->userRepository->getUserByEmail($data['email']);
            $username = 'user' . strval($savedUser->id);
            $user->setusername($username);
            $this->userRepository->editUser($user);
            return $response->withHeader('Location', '/sign-in')->withStatus(302);
        }
        return $this->twig->render(
            $response,
            'sign-up.twig',
            [
                'currentPage' => ['sign-up'],
                'notSigned' => '1',
                'formErrors' => $errors,
                'formData' => $data,
                'formAction' => $routeParser->urlFor('signUp')
            ]
        );
    }
}
