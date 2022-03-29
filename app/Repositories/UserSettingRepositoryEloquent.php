<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Repositories;

use App\Enums\Settings\SettingNameEnum;
use App\Interfaces\Repositories\UserSettingRepositoryInterface;
use App\Models\UserSetting;
use Illuminate\Support\Facades\Auth;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class UserSettingRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class UserSettingRepositoryEloquent extends BaseRepository implements UserSettingRepositoryInterface
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return UserSetting::class;
    }
    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    /**
     * @param $settingId
     * @param $userId
     * @return mixed
     */
    public function findUserSettingBySettingIdAndUserId($settingId, $userId)
    {
        return $this->model
            ->select('settings.name', 'settings.default_value', 'user_settings.value', 'user_id')
            ->where([
                'user_id' => $userId,
                'settings.id' => $settingId
            ])
            ->join('settings', 'settings.id', '=', 'setting_id');
    }

    /**
     * @param string|array $notificationSettingNames
     * @param $userId
     * @return mixed
     */
    public function getNotificationBySettingNameAndUserId($notificationSettingNames, $userId)
    {
        if (!is_array($notificationSettingNames)) {
            $notificationSettingNames = [$notificationSettingNames];
        }
        return $this->model
            ->select('settings.name', 'user_settings.value', 'user_settings.user_id')
            ->join('settings', 'settings.id', '=', 'setting_id')
            ->where('user_id', $userId)
            ->whereIn('settings.name', $notificationSettingNames);
    }

    public function settingNotification($settingName, $value)
    {
        return $this->model
            ->join('settings', 'settings.id', '=', 'setting_id')
            ->where([
                'user_id' => Auth::id(),
                'settings.name' => $settingName
            ])
            ->update(['value' => $value]);
    }
}
