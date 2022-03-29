<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Interfaces\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface OfficialNotificationRepositoryInterface.
 *
 * @package namespace App\Interfaces;
 */
interface OfficialNotificationRepositoryInterface extends RepositoryInterface
{
    public function getAvailableByDate($date);

    public function getUnreadByDateUserId($date, $userId);
}
