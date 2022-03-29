<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Services\Following;

use App\Enums\Notifications\NotificationTemplateNameEnum;
use App\Enums\Users\FollowingStatusEnum;
use App\Exceptions\BadRequestException;
use App\Interfaces\Repositories\NotificationRepositoryInterface;
use App\Interfaces\Repositories\UserFollowRepositoryInterface;
use App\Interfaces\Repositories\UserRepositoryInterface;
use App\Interfaces\Services\Following\FollowingServiceInterface;
use App\Services\BaseService;
use App\Traits\FirebaseNotificationTrait;
use App\Traits\FollowingTrait;
use App\Traits\UserTrait;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Auth;

class FollowingService extends BaseService implements FollowingServiceInterface
{
    use UserTrait, FollowingTrait, FirebaseNotificationTrait;

    public $repository;
    public $userRepository;
    public $notificationRepository;

    public function __construct(
        UserFollowRepositoryInterface $repository,
        UserRepositoryInterface $userRepository,
        NotificationRepositoryInterface $notificationRepository
    ) {
        $this->repository = $repository;
        $this->userRepository = $userRepository;
        $this->notificationRepository = $notificationRepository;
    }

    /**
     * @param $params
     * @return array
     * @throws BadRequestException
     * @throws GuzzleException
     */
    public function followUser($params): array
    {
        $currentUserId = Auth::id();
        $userIdToFollow = $params['userId'];
        if ($currentUserId == $userIdToFollow) {
            throw new BadRequestException(trans('exception.bad_request'));
        }
        $followingUser = $this->userRepository
            ->notBanned()
            ->available()
            ->where(['id' => $userIdToFollow])
            ->first();
        if (empty($followingUser)) {
            throw new BadRequestException(trans('messages.user.not_found'));
        }
        $exists = $this->repository->findByUserIdAndFollowingId($currentUserId, $userIdToFollow)->first();
        if (!empty($exists)) {
            throw new BadRequestException(trans('exception.bad_request'));
        }
        if ($this->isBlock($userIdToFollow, $currentUserId)) {
            return [
                'message' => trans('messages.user.userBlocked')
            ];
        }
        if ($this->isPublic($followingUser)) {
            $followStatus = FollowingStatusEnum::FOLLOWING;
            $message = trans('messages.user.followSuccessful', [
                'username' => $followingUser->nickname
            ]);
            $notificationType = NotificationTemplateNameEnum::FOLLOW_USER_PUBLIC;
        } else {
            $followStatus = FollowingStatusEnum::WAITING;
            $message = trans('messages.user.requestFollowSuccessful', [
                'username' => $followingUser->nickname
            ]);
            $notificationType = NotificationTemplateNameEnum::FOLLOW_USER_PRIVATE;
        }
        $userFollowRecord = $this->create([
            'user_id' => $currentUserId,
            'following_id' => $userIdToFollow,
            'status' => $followStatus
        ]);

        $notificationData = [
            'notification_type' => $notificationType,
            'notification_entity_id' => $userFollowRecord->id,
            'push_entity_type' => $this->getEntityTypeByNotificationType($notificationType),
            'push_entity_id' => Auth::id()
        ];
        $this->notificationHandle($userIdToFollow, $notificationData);

        return [
            'message' => $message
        ];
    }

    /**
     * @param $params
     * @return array
     * @throws BadRequestException
     */
    public function unFollowUser($params): array
    {
        $currentUserId = Auth::id();
        $userIdToUnFollow = $params['userId'];
        if ($this->isBlock($userIdToUnFollow, $currentUserId)) {
            return [
                'message' => trans('messages.user.userBlocked')
            ];
        }
        $followingUser = $this->userRepository
            ->notBanned()
            ->available()
            ->where(['id' => $userIdToUnFollow])
            ->first();
        if (empty($followingUser)) {
            throw new BadRequestException(trans('messages.user.not_found'));
        }
        $exists = $this->repository->findByUserIdAndFollowingId($currentUserId, $userIdToUnFollow)->first();
        if (empty($exists)) {
            throw new BadRequestException(trans('exception.bad_request'));
        }
        $this->delete($exists->id);
        if ($exists->status == FollowingStatusEnum::WAITING) {
            $this->notificationRepository->deleteNotificationByEntityAndName(
                'App\Models\UserFollow',
                $exists->id
            );
        }
        return [
            'message' => trans('messages.user.unFollowSuccessful', [
                'username' => $followingUser->nickname
            ])
        ];
    }

    /**
     * @param $params
     * @return array
     * @throws BadRequestException
     */
    public function approveFollow($params): array
    {
        $currentUserId = Auth::id();
        $userIdToHandle = $params['userId'];
        $followRequest = $this->repository->findByUserIdAndFollowingId($userIdToHandle, $currentUserId)->first();
        if (empty($followRequest) || !$this->isWaiting($followRequest)) {
            throw new BadRequestException(trans('exception.bad_request'));
        }
        $this->repository->update(['status' => FollowingStatusEnum::FOLLOWING], $followRequest->id);
        $this->notificationRepository->deleteNotificationByEntityAndName(
            'App\Models\UserFollow',
            $followRequest->id
        );
        $followerUser = $this->userRepository
            ->notBanned()
            ->available()
            ->where(['id' => $userIdToHandle])
            ->first();
        if (empty($followingUser)) {
            throw new BadRequestException(trans('messages.user.not_found'));
        }
        return [
            'message' => trans('messages.user.approveFollowSuccess', [
                'username' => $followerUser->nickname
            ])
        ];
    }

    public function rejectFollow($params): array
    {
        $currentUserId = Auth::id();
        $userIdToHandle = $params['userId'];
        $followRequest = $this->repository->findByUserIdAndFollowingId($userIdToHandle, $currentUserId)->first();
        if (empty($followRequest) || !$this->isWaiting($followRequest)) {
            throw new BadRequestException(trans('exception.bad_request'));
        }
        $this->notificationRepository->deleteNotificationByEntityAndName(
            'App\Models\UserFollow',
            $followRequest->id
        );
        $this->delete($followRequest->id);
        $followerUser = $this->userRepository
            ->notBanned()
            ->available()
            ->where(['id' => $userIdToHandle])
            ->first();
        if (empty($followingUser)) {
            throw new BadRequestException(trans('messages.user.not_found'));
        }
        return [
            'message' => trans('messages.user.rejectFollowSuccess', [
                'username' => $followerUser->nickname
            ])
        ];
    }
}
