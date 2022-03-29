<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Repositories;

use App\Enums\Notifications\NotificationTemplateNameEnum;
use App\Interfaces\Repositories\NotificationRepositoryInterface;
use App\Models\Notification;
use Illuminate\Support\Facades\DB;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class NotificationRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class NotificationRepositoryEloquent extends BaseRepository implements NotificationRepositoryInterface
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Notification::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    /**
     * @param array $nameList
     * @param $userId
     * @param false $onlyUnread
     * @return mixed
     */
    public function getByNamesAndUserId(array $nameList, $userId, $onlyUnread = false)
    {
        $queryBuilder = $this->model
            ->select('*', 'notifications.id as notification_id', 'notifications.created_at as time')
            ->join('notification_templates', 'notification_templates.id', '=', 'notifications.notification_template_id')
            ->where('receiver_id', $userId)
            ->where('notifications.deleted_at', null)
            ->whereIn('notification_templates.name', $nameList);
        if ($onlyUnread) {
            $queryBuilder->whereNull('read_at');
        }
        $queryBuilder->orderBy('notifications.id', 'desc');
        return $queryBuilder;
    }

    /**
     * @param array $nameList
     * @param $userId
     * @return mixed
     */
    public function updateReadAt(array $nameList, $userId)
    {
        return $this->model
            ->where('receiver_id', $userId)
            ->join('notification_templates', 'notification_templates.id', '=', 'notifications.notification_template_id')
            ->whereIn('notification_templates.name', $nameList)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    /**
     * @param $entityClass
     * @param $entityId
     * @param null $names
     * @return int
     */
    public function deleteNotificationByEntityAndName($entityClass, $entityId, $names = null): int
    {
        $queryBuilder = DB::table('notifications')
            ->join('notification_templates', 'notification_templates.id', '=', 'notification_template_id')
            ->where([
                'notifiable_type' => $entityClass,
                'notifiable_id' => $entityId,
            ]);
        if (!empty($names)) {
            if (!is_array($names)) {
                $names = [$names];
            }
            $queryBuilder->whereIn('name', $names);
        }
        return $queryBuilder->delete();
    }

    /**
     * @param $userId
     */
    public function deleteNotificationByUserPublic($userId)
    {
        $queryBuilder = DB::table('notifications')
            ->join('notification_templates', 'notification_templates.id', '=', 'notification_template_id')
            ->where([
                'receiver_id' => $userId,
                'notification_templates.name' => NotificationTemplateNameEnum::FOLLOW_USER_PRIVATE,
            ]);
        return $queryBuilder->delete();
    }
}
