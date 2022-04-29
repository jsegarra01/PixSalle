<?php

namespace Salle\PixSalle\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Salle\PixSalle\Repository\MySQLUserRepository;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;

class ExploreController {
    private Twig $twig;
    private MySQLUserRepository $userRepository;

    public function __construct(Twig $twig, MySQLUserRepository $userRepository) {
        $this->twig = $twig;
        $this->userRepository = $userRepository;
    }

    public function showExplorer(Request $request, Response $response): Response {
        if (!isset($_SESSION["user_id"])) {
            $routeParser = RouteContext::fromRequest($request)->getRouteParser();
            return $response
                ->withHeader('Location', $routeParser->urlFor("signIn"))
                ->withStatus(302);
        }

        return $this->twig->render(
            $response,
            'explore.twig',
            [
                'images' => $this->userRepository->getUserAllPP()
            ]
        );
    }
}