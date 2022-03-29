<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Interfaces\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface NotificationRepositoryInterface.
 *
 * @package namespace App\Interfaces;
 */
interface NotificationRepositoryInterface extends RepositoryInterface
{
    public function getByNamesAndUserId(array $nameList, $userId, $onlyUnread = false);

    public function updateReadAt(array $nameList, $userId);

    public function deleteNotificationByEntityAndName($entityClass, $entityId, $names = null);

    public function deleteNotificationByUserPublic($userId);
}
