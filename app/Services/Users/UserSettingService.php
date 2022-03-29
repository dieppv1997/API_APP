<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Services\Users;

use App\Enums\Settings\SettingNameEnum;
use App\Enums\Users\UserPublicStatusEnum;
use App\Interfaces\Repositories\NotificationRepositoryInterface;
use App\Interfaces\Repositories\SettingRepositoryInterface;
use App\Interfaces\Repositories\UserFollowRepositoryInterface;
use App\Interfaces\Repositories\UserRepositoryInterface;
use App\Interfaces\Repositories\UserSettingRepositoryInterface;
use App\Interfaces\Services\Users\UserSettingServiceInterface;
use App\Services\BaseService;
use App\Traits\HandleExceptionTrait;
use App\Traits\UserTrait;
use Illuminate\Support\Facades\Auth;

class UserSettingService extends BaseService implements UserSettingServiceInterface
{
    use HandleExceptionTrait, UserTrait;

    public $userSettingRepository;
    public $userRepository;
    public $settingRepository;
    public $userFollowRepository;
    public $notificationRepository;

    /**
     * UserSettingService constructor.
     * @param UserSettingRepositoryInterface $userSettingRepository
     * @param UserRepositoryInterface $userRepository
     * @param SettingRepositoryInterface $settingRepository
     */
    public function __construct(
        UserSettingRepositoryInterface $userSettingRepository,
        UserRepositoryInterface $userRepository,
        SettingRepositoryInterface $settingRepository,
        UserFollowRepositoryInterface $userFollowRepository,
        NotificationRepositoryInterface $notificationRepository
    ) {
        $this->userSettingRepository = $userSettingRepository;
        $this->userRepository = $userRepository;
        $this->settingRepository = $settingRepository;
        $this->userFollowRepository = $userFollowRepository;
        $this->notificationRepository = $notificationRepository;
    }

    /**
     * @param $params
     * @return array
     */
    public function settingNotification($params): array
    {
        if (isset($params['enable_notification_post'])) {
            $settingName = SettingNameEnum::ENABLE_NOTIFICATION_POST;
            $value = $params['enable_notification_post'];
            $this->userSettingRepository->settingNotification($settingName, $value);
        }
        if (isset($params['enable_notification_comment'])) {
            $settingName = SettingNameEnum::ENABLE_NOTIFICATION_COMMENT;
            $value = $params['enable_notification_comment'];
            $this->userSettingRepository->settingNotification($settingName, $value);
        }
        if (isset($params['enable_notification_following'])) {
            $settingName = SettingNameEnum::ENABLE_NOTIFICATION_FOLLOWING;
            $value = $params['enable_notification_following'];
            $this->userSettingRepository->settingNotification($settingName, $value);
        }
        return [
            'message' => trans('messages.user.settingSuccess')
        ];
    }

    /**
     * @return array
     */
    public function getSettingNotification()
    {
        $settingDefault = [
            SettingNameEnum::ENABLE_NOTIFICATION_POST => 0,
            SettingNameEnum::ENABLE_NOTIFICATION_COMMENT => 0,
            SettingNameEnum::ENABLE_NOTIFICATION_FOLLOWING => 0,
        ];
        $notificationSettingNames = array_keys($settingDefault);
        $currentUserId = Auth::id();
        $getNotificationBySettingNameAndUserId = $this->userSettingRepository->getNotificationBySettingNameAndUserId(
            $notificationSettingNames,
            $currentUserId
        )
        ->get()
        ->pluck('value', 'name')
        ->toArray();
        $settings = array_merge($settingDefault, $getNotificationBySettingNameAndUserId);
        foreach ($settings as &$setting) {
            $setting = !empty($setting);
        }
        return [
            'data' => $settings
        ];
    }

    /**
     * @param $params
     * @return array
     */
    public function settingPrivacy($params)
    {
        $userId = Auth::id();
        if (isset($params['is_public'])) {
            $this->userRepository->update([
                'is_public' => $params['is_public']
            ], Auth::id());
        }
        if ($params['is_public'] == UserPublicStatusEnum::PUBLIC_STATUS) {
            $this->userFollowRepository->updateFollowForUserPublic($userId);
            $this->notificationRepository->deleteNotificationByUserPublic($userId);
        }
        return [
            'message' => trans('messages.user.settingSuccess')
        ];
    }
}
