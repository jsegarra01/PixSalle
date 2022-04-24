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

        if (!isset($_SESSION["user_id"])) {
            $routeParser = RouteContext::fromRequest($request)->getRouteParser();
            return $response
                ->withHeader('Location', $routeParser->urlFor("sign-in"))
                ->withStatus(302);
        }

        $user = $this->userRepository->getUserByEmail($_SESSION['email']);

        $data = [];
        $data['username'] = $user->username;
        $data['email'] = $user->email;
        $data['phone'] = $user->phone;
        $data['picture'] = "No picture yet";

        # TODO create username parameter
        # TODO create phone parameter
        # TODO create picture
        
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
        #$errors['username'] = $this->validator->validateUsername($data['username']);
        #$errors['phone'] = $this->validator->validatePhone($data['phone']);
        #$errors['picture'] = $this->validator->validatePicture($data['picture']);

        #if ($errors['username'] == '') {
        #    unset($errors['username']);
        #}
        #if ($errors['phone'] == '') {
        #    unset($errors['phone']);
        #}
        #if ($errors['picture'] == '') {
        #    unset($errors['picture']);
        #}

        $user = $this->userRepository->getUserByEmail($_SESSION['email']);

        if (count($errors) == 0) {
            if($data['username']!="") {$user->username = $data['username'];}
            if($data['phone']!="") {$user->phone = $data['phone'];}
            if($data['picture']!="") {$user->picture = $data['picture'];}
            #$this->userRepository->editUser($user);
            # TODO create edit function on database
            return $response->withHeader('Location', '/profile')->withStatus(302);
        }

        $data = [];
        $data['username'] = $user->username;
        $data['email'] = $user->email;
        $data['phone'] = $user->phone;
        $data['picture'] = "No picture yet";

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