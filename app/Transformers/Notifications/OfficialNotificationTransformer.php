<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Transformers\Notifications;

use App\Enums\Notifications\NotificationTemplateNameEnum;
use App\Enums\Notifications\NotificationTypeEnum;
use App\Traits\FirebaseNotificationTrait;
use League\Fractal\TransformerAbstract;

class OfficialNotificationTransformer extends TransformerAbstract
{
    use FirebaseNotificationTrait;

    /**
     * @param array $notification
     * @return array
     */
    public function transform(array $notification): array
    {
        return [
            'actor_id' => 0,
            'actor_avatar' => null,
            'actor_name' => 'System',
            'content' => $notification['title'],
            'notification_id' => $notification['id'],
            'notification_type' => NotificationTemplateNameEnum::OFFICIAL,
            'time' => $notification['start_show_date'],
            'is_read' => !empty($notification['notification']),
            'payload' => [
                'entity_id' => $notification['id'],
                'entity_type' => NotificationTypeEnum::OFFICIAL,
                'data' => [
                    'web_link' => $notification['web_link']
                ]
            ]
        ];
    }
}
