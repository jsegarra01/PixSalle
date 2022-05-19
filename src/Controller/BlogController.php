<?php

namespace Salle\PixSalle\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\StreamInterface;
use Salle\PixSalle\Repository\MySQLBlogRepository;
use Salle\PixSalle\Repository\UserRepository;
use Salle\PixSalle\Service\ValidatorService;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;

class BlogController
{
    private Twig $twig;
    private UserRepository $userRepository;
    private MySQLBlogRepository $mySQLBlogRepository;

    public function __construct(
        Twig $twig,
        UserRepository $userRepository,
        MySQLBlogRepository $mySQLBlogRepository
    ) {
        $this->twig = $twig;
        $this->userRepository = $userRepository;
        $this->mySQLBlogRepository = $mySQLBlogRepository;
    }

    public function showBlog(Request $request, Response $response): Response {

        /*if (!isset($_SESSION["user_id"])) {
            $routeParser = RouteContext::fromRequest($request)->getRouteParser();
            return $response
                ->withHeader('Location', $routeParser->urlFor("signIn"))
                ->withStatus(302);
        }

        $user = $this->userRepository->getUserByEmail($_SESSION['email']);*/

        return $this->twig->render(
            $response,
            'blog.twig',
            [
                'currentPage' => ['blog'],
                'blogs' => $this->userRepository->getUserAllBlogs(),
            ]
        );
    }

    public function getBlog(Request $request, Response $response): Response
    {
        $blogs = $this->userRepository->getUserAllBlogs();
        $response->getBody()->write(json_encode($blogs));
        return $response;
    }

    public function postBlog(Request $request, Response $response): Response
    {
        /*if($response->getStatusCode() == http_response_code(400)) {
            $data = ['message' => "The title and/or content cannot be empty"];
            $response->getBody()->write(json_encode($data));
        } else {*/
            $data = json_decode((string) $request->getBody(), true);
            $blog = $this->userRepository->postBlog($data['title'], $data['content'], $data['userId']);
            $response->getBody()->write(json_encode($blog));
        //}

        return $response;
    }

    public function getIdBlog(Request $request, Response $response, $args):Response {
        $id = $args['id'];


        return $this->twig->render(
            $response,
            'blog.twig',
            [
                'currentPage' => ['blog'],
                'blogs' => $this->userRepository->getBlogById($id)
            ]
        );
    }

    public function getApiBlog(Request $request, Response $response, $args):Response {
        $id = $args['id'];

        /*if($response->getStatusCode() == http_response_code(404)) {
            $data = ['message' => "Blog entry with id {$id} does not exist"];
            $response->getBody()->write(json_encode($data));
        } else {*/
            $blog = $this->userRepository->getBlogById($id);
            $response->getBody()->write(json_encode($blog));
        //}

        return $response;
    }

    public function putApiBlog(Request $request, Response $response, $args):Response {
        $id = $args['id'];
        $data =json_decode((string) $request->getBody(), true);
        if (!isset($data['content']) ||  !isset($data['title'])) {
            $data = ['message' => "The title and/or content cannot be empty"];
            $response->getBody()->write(json_encode($data));
            $response->withStatus(http_response_code(400));
        } else {
            $user_exists = $this->userRepository->getBlogById($args['id']);
            if (!empty($user_exists) ) {
                $data = ['message' => "Blog entry with id {$id} does not exist"];

                $response->getBody()->write(json_encode($data));
                $response->withStatus(http_response_code(404));
            } else {
                $blog = $this->userRepository->updateBlog($id, $data['content'], $data['title']);
                $response->getBody()->write(json_encode($blog));
            }
        }

        return $response;
    }

    public function deleteApiBlog(Request $request, Response $response, $args):Response {
        $id = $args['id'];

        /*if($response->getStatusCode() == http_response_code(404)) {
            $data = ['message' => "Blog entry with id {$id} does not exist"];
            $response->getBody()->write(json_encode($data));
        } else {*/
            $this->userRepository->deleteBlogById($id);
            $data = ['message' => "The blog has been deleted"];
            $response->getBody()->write(json_encode($data));
        //}

        return $response;
    }
}