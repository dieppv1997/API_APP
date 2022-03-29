<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Interfaces\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface UserBlockRepositoryInterface.
 *
 * @package namespace App\Interfaces;
 */
interface UserBlockRepositoryInterface extends RepositoryInterface
{
    public function findByUserIdAndBlockId($userId, $blockUserId);

    public function getListBlock($userId);
}
