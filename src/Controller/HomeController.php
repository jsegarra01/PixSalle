<?php

namespace Salle\PixSalle\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class HomeController {

    private Twig $twig;

    public function __construct(Twig $twig) {
        $this->twig = $twig;
    }

    public function showLandingPage(Request $request, Response $response): Response {
        $notSigned = 1;
        if (isset($_SESSION["user_id"])) {
            $notSigned = 0;
        }

        return $this->twig->render(
            $response,
            'home.twig',
            [
                'currentPage' => ['home'],
                'notSigned' => $notSigned
            ]
        );
    }

}