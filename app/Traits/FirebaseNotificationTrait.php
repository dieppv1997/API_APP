<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Traits;

use App\Enums\Common\CommonEnum;
use App\Enums\Notifications\NotificationTemplateNameEnum;
use App\Enums\Notifications\NotificationTypeEnum;
use App\Enums\Notifications\PushNotificationModeEnum;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use LaravelFCM\Facades\FCM;
use Exception;

trait FirebaseNotificationTrait
{
    use HandleExceptionTrait;

    protected $mapEntityAndType = [
        NotificationTemplateNameEnum::LIKE_POST => 'post',
        NotificationTemplateNameEnum::NEW_COMMENT => 'post',
        NotificationTemplateNameEnum::LIKE_COMMENT => 'post',
        NotificationTemplateNameEnum::NEW_REPLY_COMMENT => 'post',
        NotificationTemplateNameEnum::FOLLOW_USER_PUBLIC => 'user',
        NotificationTemplateNameEnum::FOLLOW_USER_PRIVATE => 'user',
    ];

    protected $mapTypeAndName = [
        NotificationTypeEnum::ACTIVITY => [
            NotificationTemplateNameEnum::LIKE_COMMENT,
            NotificationTemplateNameEnum::LIKE_POST,
            NotificationTemplateNameEnum::NEW_COMMENT,
            NotificationTemplateNameEnum::FOLLOW_USER_PUBLIC,
            NotificationTemplateNameEnum::NEW_REPLY_COMMENT
        ],
        NotificationTypeEnum::FOLLOW_REQUEST => [
            NotificationTemplateNameEnum::FOLLOW_USER_PRIVATE
        ],
        NotificationTypeEnum::OFFICIAL => [
            NotificationTemplateNameEnum::OFFICIAL
        ]
    ];
    /**
     * @param $token
     * @param $pushData
     * @throws GuzzleException
     */
    public function pushDownstreamMessages($token, $pushData)
    {
        try {
            $optionBuilder = new OptionsBuilder();
            $optionBuilder->setTimeToLive(60*20);

            $notificationBuilder = new PayloadNotificationBuilder();

            $notificationBuilder->setBody($pushData['body'])
                ->setSound('default');

            $option = $optionBuilder->build();
            $notification = $notificationBuilder->build();
            if (!empty($pushData['data'])) {
                $dataBuilder = new PayloadDataBuilder();
                $dataBuilder->addData($pushData['data']);
                $data = $dataBuilder->build();
            } else {
                $data = null;
            }
            $pushNotificationMode = config('settings.pushNotificationMode');
            switch ($pushNotificationMode) {
                case PushNotificationModeEnum::PUSH_TO_APP:
                    FCM::sendTo($token, $option, $notification, $data);
                    break;
                case PushNotificationModeEnum::PUSH_TO_LOG_FILE:
                    Log::channel('notification')->info(json_encode($pushData));
                    break;
                default:
                    break;
            }

//            $numberSuccess = $downstreamResponse->numberSuccess();
//            $numberFailure = $downstreamResponse->numberFailure();
//            $numberModification = $downstreamResponse->numberModification();
//
//            // return Array - you must remove all this tokens in your database
//            $tokensToDelete = $downstreamResponse->tokensToDelete();
//
//            // return Array (key : oldToken, value : new token - you must change the token in your database)
//            $tokensToModify = $downstreamResponse->tokensToModify();
//
//            // return Array - you should try to resend the message to the tokens in the array
//            $tokensToRetry = $downstreamResponse->tokensToRetry();
//
//            // return Array (key:token, value:error) - in production you should remove from your database the tokens
//            $tokensWithError = $downstreamResponse->tokensWithError();
        } catch (Exception $e) {
            $this->handleException($e);
        }
    }

    /**
     * Push notification by type
     * - Check condition receive notification
     * - Create record on notifications table
     * - Generate message and push via firebase
     * @param int $receiverId
     * @param array $notificationData
     * @return bool
     * @throws GuzzleException
     */
    public function notificationHandle(int $receiverId, array $notificationData): bool
    {
        $notificationTplRepository = app('App\Interfaces\Repositories\NotificationTemplateRepositoryInterface');
        $notificationRepository = app('App\Interfaces\Repositories\NotificationRepositoryInterface');
        $fCMTokenRepository = app('App\Interfaces\Repositories\FCMTokenRepositoryInterface');
        $userSettingRepository = app('App\Interfaces\Repositories\UserSettingRepositoryInterface');

        $notificationTemplate = $notificationTplRepository
            ->with('notificationSetting')
            ->findWhere(['name' => $notificationData['notification_type']])->first();

        $notificationSettingName = $notificationTemplate->notificationSetting->name ?? null;

        if (!empty($notificationTemplate)) {
            $notificationRepository->create([
                'notification_template_id' => $notificationTemplate->id,
                'actor_id' => Auth::id(),
                'receiver_id' => $receiverId,
                'notifiable_id' => $notificationData['notification_entity_id'],
                'notifiable_type' => $notificationTemplate->entity_type,
                'post_id' => !empty($notificationData['post_id']) ? $notificationData['post_id'] : null,
            ]);
            $userNotificationSetting = $userSettingRepository->with('user')->getNotificationBySettingNameAndUserId(
                $notificationSettingName,
                $receiverId
            )->first();
            if (!empty($userNotificationSetting) && $userNotificationSetting->value == CommonEnum::BOOL_TRUE) {
                $fcmTokens = $fCMTokenRepository->findWhere(['user_id' => $receiverId])->pluck('fcm_token')->toArray();
                if (!empty($fcmTokens)) {
                    $bodyTemplate = $notificationTemplate->notificationTemplateTranslation->body;
                    $body = Str::replace('{username}', Auth::user()->nickname, $bodyTemplate);
                    $pushData = [
                        'body' => $body,
                        'user_id' => $receiverId,
                        'notificationData' => $notificationData,
                        'data' => [
                            'notification_type' => $notificationData['notification_type'],
                            'entity_id' => $notificationData['push_entity_id'],
                            'entity_type' => $notificationData['push_entity_type'],
                        ]
                    ];
                    $this->pushDownstreamMessages($fcmTokens, $pushData);
                }
                return true;
            }
        }
        return true;
    }

    /**
     * @param $notificationType
     * @return string|null
     */
    protected function getEntityTypeByNotificationType($notificationType): ?string
    {
        return $this->mapEntityAndType[$notificationType] ?? null;
    }

    public function getNameByType($type): ?array
    {
        return $this->mapTypeAndName[$type] ?? null;
    }
}
