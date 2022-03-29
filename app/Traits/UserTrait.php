<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Traits;

use App\Enums\Settings\SettingNameEnum;
use App\Enums\Users\FollowingStatusEnum;
use App\Enums\Users\UserBannedEnum;
use App\Enums\Users\UserPublicStatusEnum;
use App\Models\User;
use App\Models\UserBlock;
use App\Models\UserFollow;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

trait UserTrait
{
    /**
     * @param User|array $user
     * @return bool
     */
    public function isPublic($user)
    {
        if (is_array($user)) {
            return $user['is_public'] == UserPublicStatusEnum::PUBLIC_STATUS;
        }
        if (is_object($user)) {
            return $user->is_public == UserPublicStatusEnum::PUBLIC_STATUS;
        }
        return false;
    }

    /**
     * @param int $userId
     * @param int $needCheck
     * @return bool
     */
    public function isFollowingUser($userId, $needCheck)
    {
        $followingUser = UserFollow::where([
            'user_id' => $userId,
            'following_id' => $needCheck,
            'status' => FollowingStatusEnum::FOLLOWING
        ])->first();
        if (!empty($followingUser)) {
            return true;
        }
        return false;
    }

    /**
     * Return true if $userId is blocking $needCheckUserId
     * @param int $userId
     * @param int $needCheckUserId
     * @return bool
     */
    public function isBlock(int $userId, int $needCheckUserId): bool
    {
        $blockedUser = UserBlock::where([
            'user_id' => $userId,
            'block_user_id' => $needCheckUserId
        ])->first();
        return !empty($blockedUser);
    }

    /**
     * Return true if $userId is blocking $needCheckUserId
     * @param int $firstUserId
     * @param int $secondUserId
     * @return bool
     */
    public function hasBlockRelation(int $firstUserId, int $secondUserId): bool
    {
        $blockedUser = DB::table('user_blocks')
            ->where(function ($query) use ($firstUserId, $secondUserId) {
                $query->where([
                    'user_id' => $firstUserId,
                    'block_user_id' => $secondUserId
                ])
                    ->orWhere(function ($query) use ($firstUserId, $secondUserId) {
                        $query->where([
                            'user_id' => $secondUserId,
                            'block_user_id' => $firstUserId
                        ]);
                    });
            })
            ->count();
        return !empty($blockedUser);
    }

    /**
     * @param User|Authenticatable|array $user
     * @return bool
     */
    public function isBanned($user): bool
    {
        if (is_array($user)) {
            return $user['is_banned'] == UserBannedEnum::USER_BANNED;
        }
        if (is_object($user)) {
            return $user->is_banned == UserBannedEnum::USER_BANNED;
        }
        return false;
    }

    /**
     * @param $userId
     * @return bool
     */
    public function isCurrentLoggedInUser($userId)
    {
        return $userId == Auth::id();
    }

    /**
     * @param $userId
     */
    public function generateUserSetting($userId)
    {
        $settingNameNeedGenerate = [
            SettingNameEnum::ENABLE_NOTIFICATION_POST,
            SettingNameEnum::ENABLE_NOTIFICATION_COMMENT,
            SettingNameEnum::ENABLE_NOTIFICATION_FOLLOWING,
        ];
        $settingRepository = app('App\Interfaces\Repositories\SettingRepositoryInterface');
        $userSettingRepository = app('App\Interfaces\Repositories\UserSettingRepositoryInterface');
        $userSettings = [];
        foreach ($settingNameNeedGenerate as $settingName) {
            $setting = $settingRepository->findWhere(['name' => $settingName])->first();
            $userSettings[] = [
                'user_id' => $userId,
                'setting_id' => $setting->id,
                'value' => $setting->default_value,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }
        $userSettingRepository->insert($userSettings);
    }
}
