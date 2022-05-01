<?php

declare(strict_types=1);

use Salle\PixSalle\Controller\API\BlogAPIController;
use Salle\PixSalle\Controller\ExploreController;
use Salle\PixSalle\Controller\SignUpController;
use Salle\PixSalle\Controller\UserSessionController;
use Salle\PixSalle\Controller\ProfileController;
use Salle\PixSalle\Controller\PasswordController;
use Salle\PixSalle\Controller\MembershipController;
use Slim\App;

function addRoutes(App $app): void
{
    $app->get('/', UserSessionController::class . ':showSignInForm')->setName('signIn2');
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
}
