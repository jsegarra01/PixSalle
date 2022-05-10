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

class MembershipController
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

    public function showMembership(Request $request, Response $response): Response {

        if (!isset($_SESSION["user_id"])) {
            $routeParser = RouteContext::fromRequest($request)->getRouteParser();
            return $response
                ->withHeader('Location', $routeParser->urlFor("signIn"))
                ->withStatus(302);
        }

        $user = $this->userRepository->getUserByEmail($_SESSION['email']);

        $data = [];
        $data['membership'] = $user->membership;

        $messages = $this->flash->getMessages();

        $membershipError = $messages['membershipError'][0] ?? "";
        
        return $this->twig->render(
            $response,
            'membership.twig',
            [
                'currentPage' => ['user', 'membership'],
                'formData' => $data,
                'membershipError' => $membershipError
            ]
        );
    }

    public function changeMembership(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();
        
        $errors = [];
        
        $userdata = $this->userRepository->getUserByEmail($_SESSION['email']);
        $createdAt = date_create_from_format('Y-m-d H:i:s', $userdata->createdAt);
        $updatedAt = date_create_from_format('Y-m-d H:i:s', $userdata->updatedAt);

        $user = new User($userdata->email, $userdata->password, $createdAt, $updatedAt, $userdata->username, $userdata->phone, $userdata->picture);

        if (count($errors) == 0) {
            if($data['membership']!="") {$user->setmembership($data['membership']);}
            $this->userRepository->editUser($user);
            return $response->withHeader('Location', '/user/membership')->withStatus(302);
        }
        
        $data = [];
        $data['membership'] = $userdata->membership;

        return $this->twig->render(
            $response,
            'membership.twig',
            [
                'formErrors' => $errors,
                'formData' => $data,
                'formAction' => $routeParser->urlFor('membership')
            ]
        );
    }

}