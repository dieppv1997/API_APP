<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Interfaces\Repositories;

use App\Models\Post;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface PostRepositoryInterface.
 *
 * @package namespace App\Interfaces;
 */
interface PostRepositoryInterface extends RepositoryInterface
{
    public function getPostsByTag($tagId);

    public function getPostsByTagFromPost($tagId, $post);

    public function getListNewest();

    public function getListNewestFromPost($post);

    public function getPostById($postId, $onlyPublished = false);

    public function getPostsByFollowingUser(int $currentUserId);

    public function getDraftOfPostByUser();

    public function getPostByUserId($userId);

    public function getPostForEdit($postId);

    public function getPostByUserFromPost(int $userId, $post);

    public function getPostLikedOfUser(int $userId);

    public function getPostLikedOfUserFromId(int $userId, int $likedId);

    public function getPostWithAuthorById($postId);
}
