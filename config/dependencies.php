<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;
use Salle\PixSalle\Controller\ExploreController;
use Salle\PixSalle\Controller\PortfolioController;
use Salle\PixSalle\Controller\SignUpController;
use Salle\PixSalle\Controller\UserSessionController;
use Salle\PixSalle\Repository\MySQLAlbumRepository;
use Salle\PixSalle\Repository\MySQLPictureRepository;
use Salle\PixSalle\Repository\MySQLPortfolioRepository;
use Salle\PixSalle\Repository\MySQLUserRepository;
use Salle\PixSalle\Repository\PDOConnectionBuilder;
use Salle\PixSalle\Controller\ProfileController;
use Salle\PixSalle\Controller\PasswordController;
use Salle\PixSalle\Controller\MembershipController;
use Slim\Flash\Messages;
use Slim\Views\Twig;

function addDependencies(ContainerInterface $container): void
{
    $container->set(
        'view',
        function () {
            return Twig::create(__DIR__ . '/../templates', ['cache' => false]);
        }
    );

    $container->set(
        'flash',
        function () {
            return new Messages();
        }
    );

    $container->set('db', function () {
        $connectionBuilder = new PDOConnectionBuilder();
        return $connectionBuilder->build(
            $_ENV['MYSQL_ROOT_USER'],
            $_ENV['MYSQL_ROOT_PASSWORD'],
            $_ENV['MYSQL_HOST'],
            $_ENV['MYSQL_PORT'],
            $_ENV['MYSQL_DATABASE']
        );
    });

    $container->set(
        'user_repository',
        function (ContainerInterface $container) {
            return new MySQLUserRepository($container->get('db'));
    });

    $container->set(
        'portfolio_repository',
        function (ContainerInterface $container) {
            return new MySQLPortfolioRepository($container->get('db'));
    });

    $container->set(
        'album_repository',
        function (ContainerInterface $container) {
            return new MySQLAlbumRepository($container->get('db'));
        });

    $container->set(
        'picture_repository',
        function (ContainerInterface $container) {
            return new MySQLPictureRepository($container->get('db'));
        });

    $container->set(
        UserSessionController::class,
        function (ContainerInterface $c) {
            return new UserSessionController($c->get('view'), $c->get('user_repository'));
        }
    );

    $container->set(
        SignUpController::class,
        function (ContainerInterface $c) {
            return new SignUpController($c->get('view'), $c->get('user_repository'));
        }
    );

    $container->set(
        ExploreController::class,
        function (ContainerInterface $c) {
            return new ExploreController($c->get('view'), $c->get('picture_repository'));
        }
    );

    $container->set(
        ProfileController::class,
        function (ContainerInterface $c) {
            return new ProfileController($c->get('view'), $c->get('user_repository'));
        }
    );

    $container->set(
        PasswordController::class,
        function (ContainerInterface $c) {
            return new PasswordController($c->get('view'), $c->get('user_repository'));
        }
    );

    $container->set(
        MembershipController::class,
        function (ContainerInterface $c) {
            return new MembershipController($c->get('view'), $c->get('flash'), $c->get('user_repository'));
        }
    );

    $container->set(
        PortfolioController::class,
        function (ContainerInterface $c) {
            return new PortfolioController($c->get('view'),$c->get('flash') , $c->get('user_repository'), $c->get('portfolio_repository'), $c->get('album_repository'), $c->get('picture_repository'));
        }
    );
}
