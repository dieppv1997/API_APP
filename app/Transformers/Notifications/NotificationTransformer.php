<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Transformers\Notifications;

use App\Helpers\Helper;
use App\Traits\FirebaseNotificationTrait;
use Illuminate\Support\Str;
use League\Fractal\TransformerAbstract;

class NotificationTransformer extends TransformerAbstract
{
    use FirebaseNotificationTrait;

    /**
     * @param array $notification
     * @return array
     */
    public function transform(array $notification): array
    {
        $content = $notification['templates_translations']['body'];
        $notification['text'] = Str::replace('{username}', $notification['actor']['nickname'], $content);
        return [
            'actor_id' => $notification['actor']['id'],
            'actor_avatar' => Helper::generateImageUrl($notification['actor']['avatar_image']),
            'actor_name' => $notification['actor']['nickname'],
            'content' => $notification['text'],
            'notification_id' => $notification['notification_id'],
            'notification_type' => $notification['notification_templates']['name'],
            'time' => Helper::postedDateFormat($notification['time']),
            'is_read' => !empty($notification['read_at']),
            'payload' => [
                'entity_id' => $notification['payload']['entity_id'],
                'entity_type' => $notification['payload']['entity_type'],
                'data' => $notification['payload']['data']
            ]
        ];
    }
}
