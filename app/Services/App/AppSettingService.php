<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Services\App;

use App\Interfaces\Repositories\FCMTokenRepositoryInterface;
use App\Interfaces\Services\App\AppSettingServiceInterface;
use App\Services\BaseService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AppSettingService extends BaseService implements AppSettingServiceInterface
{
    public $fCMTokenRepository;

    /**
     * AppSettingService constructor.
     * @param FCMTokenRepositoryInterface $fCMTokenRepository
     */
    public function __construct(FCMTokenRepositoryInterface $fCMTokenRepository)
    {
        $this->fCMTokenRepository = $fCMTokenRepository;
    }

    /**
     * @param $params
     * @return string[]
     */
    public function storageFcmToken($params): array
    {
        $userId = Auth::id();
        $exists = $this->fCMTokenRepository->findWhere(['fcm_token' => $params['fcm_token']])->first();
        if (empty($exists)) {
            $this->fCMTokenRepository->create([
                'user_id' => $userId,
                'fcm_token' => $params['fcm_token'],
            ]);
        } else {
            if ($exists->user_id != $userId) {
                $this->fCMTokenRepository->update(['user_id' => $userId], $exists->id);
            }
        }
        return [
            'message' => trans('messages.common.success')
        ];
    }
}
