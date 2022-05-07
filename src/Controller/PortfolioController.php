<?php

namespace Salle\PixSalle\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Salle\PixSalle\Model\Album;
use Salle\PixSalle\Model\Picture;
use Salle\PixSalle\Model\Portfolio;
use Salle\PixSalle\Repository\MySQLAlbumRepository;
use Salle\PixSalle\Repository\MySQLPictureRepository;
use Salle\PixSalle\Repository\MySQLPortfolioRepository;
use Slim\Flash\Messages;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;

class PortfolioController {

    private Twig $twig;
    private Messages $flash;
    private MySQLPortfolioRepository $portfolioRepository;
    private MySQLAlbumRepository $albumRepository;
    private MySQLPictureRepository $pictureRepository;

    public function __construct(Twig $twig, Messages $flash, MySQLPortfolioRepository $portfolioRepository, MySQLAlbumRepository $albumRepository, MySQLPictureRepository $pictureRepository) {
        $this->twig = $twig;
        $this->flash = $flash;
        $this->portfolioRepository = $portfolioRepository;
        $this->albumRepository = $albumRepository;
        $this->pictureRepository = $pictureRepository;
    }

    public function showPortfolio(Request $request, Response $response): Response {
        if (!isset($_SESSION["user_id"])) {
            $routeParser = RouteContext::fromRequest($request)->getRouteParser();
            return $response
                ->withHeader('Location', $routeParser->urlFor("signIn"))
                ->withStatus(302);
        }

        $portfolioInfo = $this->portfolioRepository->getUserPortfolio($_SESSION['user_id']);
        $allAlbums = [];
        if ($portfolioInfo) {
            $allAlbums = $this->albumRepository->getAllAlbums($portfolioInfo['id']);
        }
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

    public function showAlbum(Request $request, Response $response, array $args): Response {
        if (!isset($_SESSION["user_id"])) {
            $routeParser = RouteContext::fromRequest($request)->getRouteParser();
            return $response
                ->withHeader('Location', $routeParser->urlFor("signIn"))
                ->withStatus(302);
        }

        echo $args['id'];

        $pictures = $this->pictureRepository->getAllPictures((int)$args['id']);

        return $this->twig->render(
            $response,
            'album.twig',
            [
                'albumID' => $args['id'],
                'pictures' => $pictures,
                'currentPage' => ['portfolio']
            ]);
    }

    public function createPortfolio(Request $request, Response $response): Response {
        $data = $request->getParsedBody();

        if (!empty($data['fieldInfo'])) {
            $portfolio = new Portfolio($data['fieldInfo'], $_SESSION['user_id']);

            $this->portfolioRepository->createPortfolio($portfolio);

            $routeParser = RouteContext::fromRequest($request)->getRouteParser();

            $response->getBody()->write(json_encode(['url' => $routeParser->urlFor("portfolio")]));

            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } else {

            $response->getBody()->write(json_encode(['error' => "This field cannot be empty!"]));

            return $response->withHeader('Content-Type', 'application/json')->withStatus(412);
        }
    }

    public function createAlbum(Request $request, Response $response): Response {
        $data = $request->getParsedBody();

        if (!empty($data['fieldInfo'])) {
            $portfolio = $this->portfolioRepository->getUserPortfolio($_SESSION['user_id']);

            $album = new Album($data['fieldInfo'],$portfolio['id']);

            $this->albumRepository->createAlbum($album);

            $routeParser = RouteContext::fromRequest($request)->getRouteParser();

            $response->getBody()->write(json_encode(['url' => $routeParser->urlFor("portfolio")]));

            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } else {

            $response->getBody()->write(json_encode(['error' => "This field cannot be empty!"]));

            return $response->withHeader('Content-Type', 'application/json')->withStatus(412);
        }
    }

    public function uploadPicture(Request $request, Response $response, array $args): Response{
        $data = $request->getParsedBody();

        if (!empty($data['fieldInfo'])) {
            $picture = new Picture($data['fieldInfo'], (int)$args['id']);

            $this->pictureRepository->uploadPicture($picture);

            $routeParser = RouteContext::fromRequest($request)->getRouteParser();

            $url = $routeParser->urlFor('album') . '/' . $args['id'];

            $response->getBody()->write(json_encode(['url' => $url]));

            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } else {

            $response->getBody()->write(json_encode(['error' => "This field cannot be empty!"]));

            return $response->withHeader('Content-Type', 'application/json')->withStatus(412);
        }

    }

    public function deleteAlbumPicture(Request $request, Response $response, array $args): Response {
        $data = $request->getParsedBody();

        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

        if ($data['id'] != 'album') {
            $this->pictureRepository->deletePicture((int)$data['id']);

            $url = $routeParser->urlFor('album') . '/' . $args['id'];
        } else {
            $this->pictureRepository->deleteAllPictures($args['id']);

            $this->albumRepository->deleteAlbum($args['id']);

            $url = $routeParser->urlFor('portfolio');
        }

        $response->getBody()->write(json_encode(['url' => $url]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

}




//        $_SESSION['user_id'] = $user->id;
//        $_SESSION['email'] = $data['email'];
//        $_SESSION['username'] = $user->username;