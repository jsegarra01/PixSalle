<?php

declare(strict_types=1);

namespace Salle\PixSalle\Repository;

use Salle\PixSalle\Model\User;

interface BlogRepository
{
    public function getUserAllBlogs();

    public function getBlogById(int $id);

    public function deleteBlogById(int $id);

    public function postBlog(String $title, String $content, int $user_id);

    public function updateBlog(int $id, String $content, String $title);

}
