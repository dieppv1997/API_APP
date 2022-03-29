<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Interfaces\Services\Posts;

interface PostServiceInterface
{
    public function getDetail($params);

    public function createPost($params);

    public function likePost($params);

    public function unlikePost($params);

    public function showPostForEdit($params);

    public function deletePost($params);
}
