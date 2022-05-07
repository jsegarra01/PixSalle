<?php

namespace Salle\PixSalle\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Salle\PixSalle\Repository\MySQLPictureRepository;
use Salle\PixSalle\Repository\MySQLUserRepository;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;

class ExploreController {
    private Twig $twig;
    private MySQLPictureRepository $pictureRepository;

    public function __construct(Twig $twig, MySQLPictureRepository $pictureRepository) {
        $this->twig = $twig;
        $this->pictureRepository = $pictureRepository;
    }

    // Show the profile pictures, when we implement the other section we will be updated to show the pictures of the user.
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
                'currentPage' => ['explore'],
                'images' => $this->pictureRepository->getAllPicturesUser()
            ]
        );
    }
}