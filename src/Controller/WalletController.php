<?php

namespace Salle\PixSalle\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Salle\PixSalle\Repository\UserRepository;
use Salle\PixSalle\Service\ValidatorService;
use Slim\Flash\Messages;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;

class WalletController
{

    private Twig $twig;
    private Messages $flash;
    private ValidatorService $validator;
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

    public function showWallet(Request $request, Response $response): Response
    {

        if (!isset($_SESSION["user_id"])) {
            $this->flash->addMessage(
                'signError',
                'You have to be logged in to access the WALLET!'
            );
            $routeParser = RouteContext::fromRequest($request)->getRouteParser();
            return $response
                ->withHeader('Location', $routeParser->urlFor("signIn"))
                ->withStatus(302);
        }

        $thisUser = $this->userRepository->getFunds($_SESSION["user_id"]);

        $messages = $this->flash->getMessages();

        $walletError = $messages['walletError'][0] ?? "";

        return $this->twig->render(
            $response,
            'wallet.twig',
            [
                'currentPage' => ['user', 'wallet'],
                'walletError' => $walletError,
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

        $thisUser = $this->userRepository->getFunds($_SESSION["user_id"]);

        if (empty($errors)) {
            $added = "The funds have been correctly added to your wallet";
            $funds = $this->userRepository->updateFunds($_SESSION["user_id"], $data['amount']);
        } else {
            $funds = $thisUser->funds;
        }


        return $this->twig->render(
            $response,
            'wallet.twig',
            [
                'currentPage' => ['user', 'wallet'],
                'formErrors' => $errors,
                'addedFunds' => $added,
                'funds' => $funds,
                'user' => $thisUser->username,
            ]);
    }
}