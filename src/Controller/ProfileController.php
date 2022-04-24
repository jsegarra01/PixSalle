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
        # TODO add validation of session started
        return $this->twig->render($response, 'profile.twig');
    }

    public function editProfile(Request $request, Response $response): Response
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
                return $response->withHeader('Location','/')->withStatus(302);
            }
        }
        return $this->twig->render(
            $response,
            'sign-in.twig',
            [
                'formErrors' => $errors,
                'formData' => $data,
                'formAction' => $routeParser->urlFor('signIn')
            ]
        );
    }

}