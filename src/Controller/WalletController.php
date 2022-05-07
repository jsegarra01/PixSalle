<?php

namespace Salle\PixSalle\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Salle\PixSalle\Repository\UserRepository;
use Salle\PixSalle\Service\ValidatorService;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;

class WalletController
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

    public function showWallet(Request $request, Response $response): Response
    {

        if (!isset($_SESSION["user_id"])) {
            $routeParser = RouteContext::fromRequest($request)->getRouteParser();
            return $response
                ->withHeader('Location', $routeParser->urlFor("signIn"))
                ->withStatus(302);
        }

        $thisUser = $this->userRepository->getFunds($_SESSION["user_id"]);

        return $this->twig->render(
            $response,
            'wallet.twig',
            [
                'currentPage' => ['user', 'wallet'],
                'funds' => $thisUser->funds,
            ]);
    }

    public function postMoney(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();

        $errors['amount'] = $this->validator->validateAmount($data['amount']);

        if ($errors['amount'] == '') {
            unset($errors['amount']);
        }

        if (empty($errors)) {
            $added = "The funds have been correctly added to your wallet";
            $funds = $this->userRepository->updateFunds($_SESSION["user_id"], $data['amount']);
        }


        return $this->twig->render(
            $response,
            'wallet.twig',
            [
                'currentPage' => ['user', 'wallet'],
                'formErrors' => $errors,
                'addedFunds' => $added,
                'funds' => $funds,
            ]);
    }
}