<?php

declare(strict_types=1);

use Salle\PixSalle\Controller\HomeController;
use Salle\PixSalle\Controller\PortfolioController;
use Salle\PixSalle\Controller\API\BlogAPIController;
use Salle\PixSalle\Controller\ExploreController;
use Salle\PixSalle\Controller\SignUpController;
use Salle\PixSalle\Controller\UserSessionController;
use Salle\PixSalle\Controller\ProfileController;
use Salle\PixSalle\Controller\PasswordController;
use Salle\PixSalle\Controller\MembershipController;
use Salle\PixSalle\Controller\WalletController;
use Slim\App;

function addRoutes(App $app): void
{
    $app->get('/', HomeController::class . ':showLandingPage')->setName('home');
    $app->get('/sign-in', UserSessionController::class . ':showSignInForm')->setName('signIn');
    $app->post('/sign-in', UserSessionController::class . ':signIn');
    $app->get('/sign-up', SignUpController::class . ':showSignUpForm')->setName('signUp');
    $app->post('/sign-up', SignUpController::class . ':signUp');

    $app->get('/explore', ExploreController::class . ':showExplorer')->setName('explore');

    $app->get('/profile', ProfileController::class . ':showProfile')->setName('profile');
    $app->post('/profile', ProfileController::class . ':editProfile');

    $app->get('/profile/changePassword', PasswordController::class . ':showChangePassword')->setName('changePassword');
    $app->post('/profile/changePassword', PasswordController::class . ':changePassword');

    $app->get('/user/membership', MembershipController::class . ':showMembership')->setName('membership');
    $app->post('/user/membership', MembershipController::class . ':changeMembership');

    $app->get('/portfolio', PortfolioController::class .':showPortfolio')->setName('portfolio');
    $app->post('/portfolio', PortfolioController::class .':createPortfolio');

    $app->post('/portfolio/album', PortfolioController::class .':createAlbum')->setName('album');

    $app->post('/portfolio/album/qr/{id}', PortfolioController::class .':generateQR')->setName('qr');
    $app->get('/portfolio/album/qr/{id}', PortfolioController::class .':downloadQR');

    $app->get('/portfolio/album/{id}', PortfolioController::class .':showAlbum')->setName('picture');
    $app->post('/portfolio/album/{id}', PortfolioController::class .':uploadPicture');
    $app->delete('/portfolio/album/{id}', PortfolioController::class .':deleteAlbumPicture');

    $app->get('/user/wallet', WalletController::class . ':showWallet')->setName('wallet');
    $app->post('/user/wallet', WalletController::class . ':postMoney');

}
