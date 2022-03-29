<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Interfaces\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface UserSettingRepositoryInterface.
 *
 * @package namespace App\Interfaces;
 */
interface UserSettingRepositoryInterface extends RepositoryInterface
{
    public function findUserSettingBySettingIdAndUserId($settingId, $userId);

    public function settingNotification($settingName, $value);

    /**
     * @param string|array $settingNames
     * @param $userId
     * @return mixed
     */
    public function getNotificationBySettingNameAndUserId($settingNames, $userId);
}
