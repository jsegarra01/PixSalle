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
    private MySQLBlogRepository $mySQLBlogRepository;

    public function __construct(
        Twig $twig,
        MySQLBlogRepository $mySQLBlogRepository
    ) {
        $this->twig = $twig;
        $this->mySQLBlogRepository = $mySQLBlogRepository;
    }

    public function showBlog(Request $request, Response $response): Response {

        return $this->twig->render(
            $response,
            'blog.twig',
            [
                'currentPage' => ['blog'],
                'blogs' => $this->mySQLBlogRepository->getUserAllBlogs(),
            ]
        );
    }

    public function getBlog(Request $request, Response $response): Response
    {
        $blogs = $this->mySQLBlogRepository->getUserAllBlogs();
        $response->getBody()->write(json_encode($blogs));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function postBlog(Request $request, Response $response): Response {
        $data = $request->getParsedBody();
        if (empty($data['content']) ||  empty($data['title']) || empty($data['userId'])) {
            $message = ['message' => "'title' and/or 'content' and/or 'userId' key missing"];
            $code = 400;
        } else {
            $data = json_decode((string) $request->getBody(), true);
            $message = $this->mySQLBlogRepository->postBlog($data['title'], $data['content'], $data['userId']);
            $code = 201;
        }
        $response->getBody()->write(json_encode($message));
        return $response->withHeader('Content-Type', 'application/json')->withStatus($code);
    }

    public function getIdBlog(Request $request, Response $response, $args):Response {
        $id = $args['id'];
        return $this->twig->render(
            $response,
            'individualBlog.twig',
            [
                'currentPage' => ['blog'],
                'blogs' => $this->mySQLBlogRepository->getBlogById($id)
            ]
        );
    }

    public function getApiBlog(Request $request, Response $response, $args):Response {
        $id = $args['id'];
        $message = $this->mySQLBlogRepository->getBlogById($id);
        $code = 200;
        if(empty($message)) {
            $message = ['message' => "Blog entry with id {$id} does not exist"];
            $code = 404;
        }
        $response->getBody()->write(json_encode($message));
        return $response->withHeader('Content-Type', 'application/json')->withStatus($code);
    }

    public function putApiBlog(Request $request, Response $response, $args):Response {
        $id = $args['id'];
        $data =json_decode((string) $request->getBody(), true);
        if (!isset($data['content']) ||  !isset($data['title'])) {
            $message = ['message' => "'title' and/or 'content' key missing"];
            $code = 400;
        } else {
            $user_exists = $this->mySQLBlogRepository->getBlogById($id);
            if (empty($user_exists) ) {
                $message = ['message' => "Blog entry with id {$id} does not exist"];
                $code = 404;
            } else {
                $message = $this->mySQLBlogRepository->updateBlog($id, $data['content'], $data['title']);
                $code = 200;
            }
        }
        $response->getBody()->write(json_encode($message));
        return $response->withHeader('Content-Type', 'application/json')->withStatus($code);
    }

    public function deleteApiBlog(Request $request, Response $response, $args):Response {
        $id = $args['id'];
        $user_exists = $this->mySQLBlogRepository->getBlogById($id);
        if(empty($user_exists)) {
            $message = ['message' => "Blog entry with id {$id} does not exist"];
            $code = 404;
        } else {
            $this->mySQLBlogRepository->deleteBlogById($id);
            $message = ['message' => "Blog entry with id {$id} was successfully deleted"];
            $code = 200;
        }

        $response->getBody()->write(json_encode($message));
        return $response->withHeader('Content-Type', 'application/json')->withStatus($code);
    }
}