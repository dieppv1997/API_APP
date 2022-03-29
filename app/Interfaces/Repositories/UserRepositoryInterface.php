<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Interfaces\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface UserRepositoryInterface.
 *
 * @package namespace App\Interfaces;
 */
interface UserRepositoryInterface extends RepositoryInterface
{
    public function searchNickNameByKeySearch($keySearch);

    public function getById($id);

    public function getListFollower($userId);

    public function getListFollowing($userId);
}
