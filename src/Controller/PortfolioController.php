<?php

namespace Salle\PixSalle\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Salle\PixSalle\Model\Album;
use Salle\PixSalle\Model\Portfolio;
use Salle\PixSalle\Repository\MySQLAlbumRepository;
use Salle\PixSalle\Repository\MySQLPortfolioRepository;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;

class PortfolioController {

    private Twig $twig;
    private MySQLPortfolioRepository $portfolioRepository;
    private MySQLAlbumRepository $albumRepository;

    public function __construct(Twig $twig, MySQLPortfolioRepository $portfolioRepository, MySQLAlbumRepository $albumRepository) {
        $this->twig = $twig;
        $this->portfolioRepository = $portfolioRepository;
        $this->albumRepository = $albumRepository;
    }

    public function showPortfolio(Request $request, Response $response, array $args): Response {
        if (!isset($_SESSION["user_id"])) {
            $routeParser = RouteContext::fromRequest($request)->getRouteParser();
            return $response
                ->withHeader('Location', $routeParser->urlFor("signIn"))
                ->withStatus(302);
        }

//        $portfolio = new Portfolio("what", $_SESSION['user_id']);
//        $this->portfolioRepository->createPortfolio($portfolio);

        $portfolioInfo = $this->portfolioRepository->getUserPortfolio($_SESSION['user_id']);
//        echo $portfolioInfo['id'];
        $allAlbums = [];
        if ($portfolioInfo) {
//            $album = new Album("album2",$portfolioInfo['id']);
//            $this->albumRepository->createAlbum($album);
            $allAlbums = $this->albumRepository->getAllAlbums($portfolioInfo['id']);
        }
//        print_r($portfolioInfo);
//        print_r($allAlbums);
//        echo $response->getBody();
        return $this->twig->render(
            $response,
            'portfolio.twig',
            [
                'portfolio' => $portfolioInfo,
                'albums' => $allAlbums,
                'currentPage' => ['portfolio']
            ]
        );
    }

    public function createPortfolio(Request $request, Response $response): Response {
        $data = $request->getParsedBody();

        $portfolio = new Portfolio($data['title'], $_SESSION['user_id']);

        $this->portfolioRepository->createPortfolio($portfolio);

        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

        return $response
            ->withHeader('Location', $routeParser->urlFor("portfolio"))
            ->withStatus(302);
    }

    public function createAlbum(Request $request, Response $response): Response {
        $data = $request->getParsedBody();

        $portfolio = $this->portfolioRepository->getUserPortfolio($_SESSION['user_id']);

        $album = new Album($data['title'],$portfolio['id']);

        $this->albumRepository->createAlbum($album);

        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

        return $response
            ->withHeader('Location', $routeParser->urlFor("portfolio"))
            ->withStatus(302);
    }

}




//        $_SESSION['user_id'] = $user->id;
//        $_SESSION['email'] = $data['email'];
//        $_SESSION['username'] = $user->username;