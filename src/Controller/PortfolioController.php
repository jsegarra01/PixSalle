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
use Salle\PixSalle\Repository\MySQLUserRepository;
use Slim\Flash\Messages;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;

class PortfolioController {

    private Twig $twig;
    private Messages $flash;
    private MySQLUserRepository $userRepository;
    private MySQLPortfolioRepository $portfolioRepository;
    private MySQLAlbumRepository $albumRepository;
    private MySQLPictureRepository $pictureRepository;

    public function __construct(Twig $twig, Messages $flash, MySQLUserRepository $userRepository, MySQLPortfolioRepository $portfolioRepository, MySQLAlbumRepository $albumRepository, MySQLPictureRepository $pictureRepository) {
        $this->twig = $twig;
        $this->flash = $flash;
        $this->userRepository = $userRepository;
        $this->portfolioRepository = $portfolioRepository;
        $this->albumRepository = $albumRepository;
        $this->pictureRepository = $pictureRepository;
    }

    public function showPortfolio(Request $request, Response $response): Response {
        if (!isset($_SESSION["user_id"])) {
            $routeParser = RouteContext::fromRequest($request)->getRouteParser();
            $this->flash->addMessage(
                'signError',
                'You have to be logged in to access to PORTFOLIO!'
            );
            return $response
                ->withHeader('Location', $routeParser->urlFor("signIn"))
                ->withStatus(302);
        }

        $portfolioInfo = $this->portfolioRepository->getUserPortfolio($_SESSION['user_id']);
        $allAlbums = [];
        if ($portfolioInfo) {
            $allAlbums = $this->albumRepository->getAllAlbums($portfolioInfo['id']);
        }

        $messages = $this->flash->getMessages();

        $qrError = $messages['qrError'][0] ?? "";

        $portfolioError = $messages['portfolioError'][0] ?? "";

        return $this->twig->render(
            $response,
            'portfolio.twig',
            [
                'portfolio' => $portfolioInfo,
                'albums' => $allAlbums,
                'currentPage' => ['portfolio'],
                'qrError' => $qrError,
                'portfolioError' => $portfolioError
            ]
        );
    }

    public function showAlbum(Request $request, Response $response, array $args): Response {
        $notSigned = 1;
        $isAlbumOwner = 0;
        if (isset($_SESSION["user_id"])) {
            $notSigned = 0;
            if ($this->albumRepository->isOwner($_SESSION["user_id"],(int)$args['id'])) {
                $isAlbumOwner = 1;
            }
        }

        $albumName = $this->albumRepository->getAlbum((int)$args['id']);
        if (!$albumName) {

            $this->flash->addMessage(
                'portfolioError',
                'The album you tried to access does not exists!'
            );

            $routeParser = RouteContext::fromRequest($request)->getRouteParser();

            return $response
                ->withHeader('Location', $routeParser->urlFor("portfolio"))
                ->withStatus(412);
        }

        $messages = $this->flash->getMessages();

        $albumError = $messages['albumError'][0] ?? "";
        $pictures = $this->pictureRepository->getAllPictures((int)$args['id']);

        return $this->twig->render(
            $response,
            'album.twig',
            [
                'albumID' => $args['id'],
                'albumName' => $albumName['name'],
                'pictures' => $pictures,
                'currentPage' => ['portfolio'],
                'albumError' => $albumError,
                'isAlbumOwner' => $isAlbumOwner,
                'notSigned' => $notSigned
            ]);
    }

    public function createPortfolio(Request $request, Response $response): Response {
        $data = $request->getParsedBody();
        $dataResponse = [];
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();
        if (isset($_SESSION["user_id"])) {
            if (!empty($data['fieldInfo'])) {
                $portfolio = new Portfolio($data['fieldInfo'], $_SESSION['user_id']);

                $this->portfolioRepository->createPortfolio($portfolio);

                $response->getBody()->write(json_encode(['url' => $routeParser->urlFor("portfolio")]));

                return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
            } else {
                $dataResponse['error'] = "This field cannot be empty!";
            }
        } else {
            $dataResponse['url'] = $routeParser->urlFor("signIn");
            $this->flash->addMessage(
                'signError',
                'You have to be logged in to create an Album!'
            );
        }
        $response->getBody()->write(json_encode($dataResponse));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(412);

    }

    public function createAlbum(Request $request, Response $response): Response {
        $data = $request->getParsedBody();
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();
        $dataResponse = [];
        if (isset($_SESSION["user_id"])) {
            $user = $this->userRepository->getUserByEmail($_SESSION['email']);
            if ($user->membership == 'Active') {
                if (!empty($data['fieldInfo'])) {
                    $portfolio = $this->portfolioRepository->getUserPortfolio($_SESSION['user_id']);

                    $album = new Album($data['fieldInfo'],$portfolio['id']);

                    $this->albumRepository->createAlbum($album);

                    $dataResponse['url'] = $routeParser->urlFor("portfolio");

                    $response->getBody()->write(json_encode($dataResponse));

                    return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
                } else {
                    $dataResponse['error'] = "This field cannot be empty!";
                }

            } else {
                $dataResponse['url'] = $routeParser->urlFor("membership");
                $this->flash->addMessage(
                    'membershipError',
                    'You have to be an ACTIVE user to create an Album!'
                );
            }
        } else {
            $dataResponse['url'] = $routeParser->urlFor("signIn");
            $this->flash->addMessage(
                'signError',
                'You have to be logged in to create an Album!'
            );
        }

        $response->getBody()->write(json_encode($dataResponse));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(412);
    }

    public function generateQR(Request $request, Response $response, array $args): Response {
        $target_dir = "QRcodes/";

        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

        $data = array(
            'symbology' => 'QRCode',
            'code' => 'http://localhost:8030' . $routeParser->urlFor('album') .'/' . $args['id']
        );

        $options = array(
            'http' => array(
                'method' => 'POST',
                'content' => json_encode($data),
                'header' => "Content-Type: application/json\r\n" .
                    "Accept: image/png\r\n"
            )
        );

        $tempQRPath = $target_dir . "qr.png";

        $uuid = uniqid("qr", false) . '.' . 'png';
        $target_file = $target_dir . $uuid;

        $context = stream_context_create($options);
        $url = 'http://barcode/BarcodeGenerator';
        $APIResponse = file_get_contents($url, false, $context);
        if (file_put_contents($target_file, $APIResponse)) {
            $this->albumRepository->addQRAlbum((int)$args['id'], $target_file);
        } else {
            unlink($tempQRPath);
            $this->flash->addMessage(
                'qrError',
                'Could not generate QR!'
            );
        }

        return $response
            ->withHeader('Location', $routeParser->urlFor("portfolio"))
            ->withStatus(200);
    }

    public function downloadQR(Request $request, Response $response, array $args): Response {

        $qrImage = $this->albumRepository->getQRAlbum((int)$args['id']);

        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

        header("Cache-Control: public");
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=username.jpeg");
        header("Content-Type: application/zip");
        header("Content-Transfer-Encoding: binary");

        readfile($qrImage['qr_image']);

        return $response
            ->withHeader('Location', $routeParser->urlFor("portfolio"))
            ->withStatus(200);
    }

    public function uploadPicture(Request $request, Response $response, array $args): Response{
        $data = $request->getParsedBody();
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();
        $dataResponse = [];
        if (isset($_SESSION["user_id"])) {
            if (!empty($data['fieldInfo'])) {
                $picture = new Picture($data['fieldInfo'], (int)$args['id']);

                $this->pictureRepository->uploadPicture($picture);

                $routeParser = RouteContext::fromRequest($request)->getRouteParser();

                $dataResponse['url'] = $routeParser->urlFor('album') . '/' . $args['id'];

                $response->getBody()->write(json_encode($dataResponse));

                return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
            } else {

                $dataResponse['error'] = "This field cannot be empty!";
            }
        } else {
            $dataResponse['url'] = $routeParser->urlFor("signIn");
            $this->flash->addMessage(
                'signError',
                'You have to be logged in to upload a Picture!'
            );
        }

        $response->getBody()->write(json_encode($dataResponse));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(412);
    }

    public function deleteAlbumPicture(Request $request, Response $response, array $args): Response {
        $data = $request->getParsedBody();
        $dataResponse = [];
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();
        if (isset($_SESSION["user_id"])) {
            if ($this->albumRepository->isOwner($_SESSION["user_id"],(int)$args['id'])) {
                if ($data['id'] != 'album') {
                    $this->pictureRepository->deletePicture((int)$data['id']);

                    $dataResponse['url'] = $routeParser->urlFor('album') . '/' . $args['id'];
                } else {
                    $this->pictureRepository->deleteAllPictures((int)$args['id']);

                    $qrPath = $this->albumRepository->getQRAlbum((int)$args['id']);

                    if ($qrPath['qr_image']) {
                        unlink($qrPath['qr_image']);
                    }

                    $this->albumRepository->deleteAlbum((int)$args['id']);

                    $dataResponse['url'] = $routeParser->urlFor('portfolio');
                }
            } else {
                $dataResponse['url'] = $routeParser->urlFor('album') . '/' . $args['id'];
                $this->flash->addMessage(
                    'albumError',
                    'You have to be the owner to do this action!'
                );
            }

            $response->getBody()->write(json_encode($dataResponse));

            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } else {
            $dataResponse['url'] = $routeParser->urlFor("signIn");
            $this->flash->addMessage(
                'signError',
                'You have to be logged in to upload a Picture!'
            );
        }

        $response->getBody()->write(json_encode($dataResponse));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

}