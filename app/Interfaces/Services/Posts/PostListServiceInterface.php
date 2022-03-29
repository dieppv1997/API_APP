<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Interfaces\Services\Posts;

interface PostListServiceInterface
{
    public function listByTag($params);

    public function listByTagFromId($params);

    public function getListNewest($params);

    public function listNewestFromId($params);

    public function listByFollowingUser($params);

    public function listDraftByUser($params);

    public function listMyPostOfUser($params);

    public function listMyPostFromId($params);

    public function listPostLiked($params);

    public function listPostLikedFromPostId($params);
}
