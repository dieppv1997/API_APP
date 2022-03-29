<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Interfaces\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface UserFollowRepositoryInterface.
 *
 * @package namespace App\Interfaces;
 */
interface UserFollowRepositoryInterface extends RepositoryInterface
{
    public function checkCurrentFollow();

    public function findByUserIdAndFollowingId($userId, $followingId);

    public function getListRecommendUser($params, $checkCurrentFollow);

    public function getFollowingOfUserId($userId);

    public function getFollowerOfUserId($userId);

    public function updateFollowForUserPublic($userId);
}
