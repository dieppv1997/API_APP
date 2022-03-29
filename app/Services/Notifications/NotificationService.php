<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Services\Notifications;

use App\Enums\Notifications\NotificationTemplateNameEnum;
use App\Enums\Notifications\NotificationTypeEnum;
use App\Exceptions\NotFoundException;
use App\Helpers\Helper;
use App\Interfaces\Repositories\NotificationRepositoryInterface;
use App\Interfaces\Repositories\OfficialNotificationRepositoryInterface;
use App\Interfaces\Services\Notifications\NotificationServiceInterface;
use App\Services\BaseService;
use App\Traits\FirebaseNotificationTrait;
use App\Transformers\Notifications\NotificationTransformer;
use App\Transformers\Notifications\OfficialNotificationTransformer;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class NotificationService extends BaseService implements NotificationServiceInterface
{
    use FirebaseNotificationTrait;

    public $notificationRepository;
    public $officialNotificationRepository;

    /**
     * @param NotificationRepositoryInterface $notificationRepository
     * @param OfficialNotificationRepositoryInterface $officialNotificationRepository
     */
    public function __construct(
        NotificationRepositoryInterface $notificationRepository,
        OfficialNotificationRepositoryInterface $officialNotificationRepository
    ) {
        $this->notificationRepository = $notificationRepository;
        $this->officialNotificationRepository = $officialNotificationRepository;
    }

    /**
     * @param $params
     * @return array
     * @throws NotFoundException
     */
    public function getListByUser($params): array
    {
        if ($params['type'] == NotificationTypeEnum::OFFICIAL) {
            $return = $this->getOfficialNotificationData($params);
        } else {
            $return = $this->getNormalNotificationData($params);
        }
        return $return;
    }

    /**
     * @param $params
     * @return array
     */
    public function getOfficialNotificationData($params): array
    {
        $currentDate = Carbon::now()->toDateString();
        $paginateData = $this->officialNotificationRepository->getAvailableByDate($currentDate)
            ->with('notification')
            ->paginate($params['per_page'], ['*'], 'currentPage', $params['current_page']);
        $paginateData = $paginateData->toArray();
        $results = $paginateData['data'];
        $return = fractal($results, new OfficialNotificationTransformer())->toArray();
        $return['current_page'] = $paginateData['current_page'];
        $return['total_page'] = ceil($paginateData['total'] / $params['per_page']);
        return $return;
    }

    /**
     * @param $params
     * @return array
     * @throws NotFoundException
     */
    public function getNormalNotificationData($params): array
    {
        $currentUserId = Auth::id();
        $typeNames = $this->getNameByType($params['type']);
        $paginateData = $this->notificationRepository->getByNamesAndUserId($typeNames, $currentUserId)
            ->with(['notificationTemplates', 'actor', 'templatesTranslations'])
            ->paginate($params['per_page'], ['*'], 'currentPage', $params['current_page']);
        foreach ($paginateData as &$item) {
            $data = [];
            $classOfEntity = $item->entity_type;
            $notificationType = $item->notificationTemplates->name;
            $entityType = $this->getEntityTypeByNotificationType($notificationType);
            $entityId = null;
            switch ($classOfEntity) {
                case 'App\Models\UserFollow':
                    $entityId = $item->actor_id;
                    if ($notificationType == NotificationTemplateNameEnum::FOLLOW_USER_PRIVATE) {
                        if (!empty($item->notifiable)) {
                            $data['status_follow'] = $item->notifiable->status;
                        }
                    }
                    break;
                case 'App\Models\Post':
                case 'App\Models\Comment':
                    $data['post_image'] = Helper::generateImageUrl($item->post->image);
                    $entityId = $item->post->id;
                    break;
                default:
                    throw new NotFoundException(trans('exception.record_not_found'));
                    break;
            }
            $item['payload'] = [
                'entity_type' => $entityType,
                'entity_id' => $entityId,
                'data' => $data
            ];
        }
        $paginateData = $paginateData->toArray();
        $results = $paginateData['data'];
        $return = fractal($results, new NotificationTransformer())->toArray();
        $return['current_page'] = $paginateData['current_page'];
        $return['total_page'] = ceil($paginateData['total'] / $params['per_page']);
        return $return;
    }

    /**
     * @return array[]
     */
    public function getStatus(): array
    {
        $newNotificationStatus = [];
        $listType = [
            NotificationTypeEnum::ACTIVITY,
            NotificationTypeEnum::FOLLOW_REQUEST,
        ];
        $userId = Auth::id();
        foreach ($listType as $type) {
            $listName = $this->getNameByType($type);
            $newNotificationStatus[$type] = $this->notificationRepository
                ->getByNamesAndUserId($listName, $userId, true)
                ->exists();
        }

        $currentDate = Carbon::now()->toDateString();
        $newNotificationStatus[NotificationTypeEnum::OFFICIAL] = $this->officialNotificationRepository
            ->getUnreadByDateUserId($currentDate, $userId)->exists();

        return [
            'data' => [
                'has_new_notification' => in_array(true, $newNotificationStatus),
                'detail' => $newNotificationStatus
            ]
        ];
    }

    /**
     * @param $params
     * @return array
     * @throws NotFoundException
     */
    public function makeOfficialAsRead($params): array
    {
        $officialNotification = $this->officialNotificationRepository->find($params['notificationId']);
        if (empty($officialNotification)) {
            throw new NotFoundException(trans('exception.record_not_found'));
        }
        $notification = $this->notificationRepository->findWhere([
            'receiver_id' => Auth::id(),
            'notifiable_type' => 'App\Models\OfficialNotification',
            'notifiable_id' => $officialNotification->id
        ])->first();
        if (empty($notification)) {
            $this->notificationRepository->create([
                'notification_template_id' => 0,
                'actor_id' => 0,
                'receiver_id' => Auth::id(),
                'notifiable_id' => $officialNotification->id,
                'notifiable_type' => 'App\Models\OfficialNotification',
                'read_at' => Carbon::now(),
            ]);
        }
        return [
            'message' => trans('messages.common.success')
        ];
    }

    public function markAsReadByType($params): array
    {
        $currentUserId = Auth::id();
        $typeNames = $this->getNameByType($params['type']);
        $this->notificationRepository->updateReadAt($typeNames, $currentUserId);
        return [
            'message' => trans('messages.common.success')
        ];
    }
}
