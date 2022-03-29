<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Services\Users;

use App\Enums\Users\FollowingStatusEnum;
use App\Exceptions\NotFoundException;
use App\Interfaces\Repositories\UserFollowRepositoryInterface;
use App\Interfaces\Services\Users\UserServiceInterface;
use App\Interfaces\Repositories\UserRepositoryInterface;
use App\Interfaces\Repositories\VerifyUserRepositoryInterface;

use App\Models\User;
use App\Services\BaseService;
use App\Traits\HandleExceptionTrait;
use App\Traits\UserTrait;
use App\Transformers\Users\ListFollowUserTransformer;
use App\Transformers\Users\UserDetailTransformer;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class UserService extends BaseService implements UserServiceInterface
{
    use HandleExceptionTrait, UserTrait;

    public $verifyUserRepository;
    public $userFollowRepository;

    /**
     * UserService constructor.
     * @param UserRepositoryInterface $repository
     * @param VerifyUserRepositoryInterface $verifyUserRepository
     * @param UserFollowRepositoryInterface $userFollowRepository
     */
    public function __construct(
        UserRepositoryInterface       $repository,
        VerifyUserRepositoryInterface $verifyUserRepository,
        UserFollowRepositoryInterface $userFollowRepository
    ) {
        $this->repository = $repository;
        $this->verifyUserRepository = $verifyUserRepository;
        $this->userFollowRepository = $userFollowRepository;
    }

    /**
     * @param $user
     * @return array
     * @throws NotFoundException
     */
    public function getDetail($user)
    {
        $currentUserId = $user['id'];
        $user = $this->repository->find($currentUserId);
        if (empty($user)) {
            throw new NotFoundException(trans('exception.record_not_found'));
        }
        $user->following_count=$this->userFollowRepository->getFollowingOfUserId($user['id'])->count();
        $user->follower_count=$this->userFollowRepository->getFollowerOfUserId($user['id'])->count();
        $user = fractal($user, new UserDetailTransformer())->toArray();
        return $user;
    }

    /**
     * @param $params
     * @return array
     * @throws NotFoundException
     */
    public function profile($params): array
    {
        $isCurrentUser = $this->isCurrentLoggedInUser($params['userId']);
        $user = $this->repository->getById($params['userId'])->first();
        if (empty($user)) {
            throw new NotFoundException(trans('exception.record_not_found'));
        }
        $user->following_count=$this->userFollowRepository->getFollowingOfUserId($params['userId'])->count();
        $user->follower_count=$this->userFollowRepository->getFollowerOfUserId($params['userId'])->count();
        $userFormatted = fractal($user, new UserDetailTransformer())->toArray();
        $userFormatted['data']['is_current_user'] = $isCurrentUser;
        $userFormatted['data']['follow_status'] = $user->following->status ?? 0;
        $userFormatted['data']['block_user'] = !$isCurrentUser && !empty($user->blockByLoggedInUser);
        $userFormatted['data']['user_block'] = !$isCurrentUser && !empty($user->blockLoggedInUser);
        return $userFormatted;
    }

    /**
     * @param $params
     * @return array
     */
    public function updateProfile($params): array
    {
        $user = Auth::user();
        $updateData = [
            'nickname' => $params['nickname'],
            'birthday' => !empty($params['birthday']) ? $params['birthday'] : null,
            'gender' => !empty($params['gender']) ? $params['gender'] : null,
            'favorite_shop' => !empty($params['favorite_shop']) ? $params['favorite_shop'] : null,
            'favorite_place' => !empty($params['favorite_place']) ? $params['favorite_place'] : null,
            'intro' => !empty($params['intro']) ? $params['intro'] : null,
            'province_id' => !empty($params['province_id']) ? $params['province_id'] : null,
        ];
        $oldAvatar = $user->avatar_image;
        $changeAvatar = !empty($params['avatar_image']);
        if ($changeAvatar) {
            $updateData['avatar_image'] = $this->generateUserImagePath(
                $user,
                $params['avatar_image'],
                'avatarImage'
            );
        }
        $oldCover = $user->cover_image;
        $changeCover = !empty($params['cover_image']);
        if ($changeCover) {
            $updateData['cover_image'] = $this->generateUserImagePath(
                $user,
                $params['cover_image'],
                'coverImage'
            );
        }
        $this->repository->update($updateData, $user->id);
        return [
            'changeAvatar' => $changeAvatar,
            'oldAvatar' => $oldAvatar,
            'newAvatar' => !empty($updateData['avatar_image']) ? $updateData['avatar_image'] : null,
            'changeCover' => $changeCover,
            'oldCover' => $oldCover,
            'newCover' => !empty($updateData['cover_image']) ? $updateData['cover_image'] : null,
        ];
    }

    /**
     * Generate post image path, contain date, random string and post id
     * Example: covers/2021/12/23/KJLzNnWHHyDdyYyVPqGA8YKu8RbB1c-38.jpg
     * Example: avatars/2021/12/23/KJLzNnWHHyDdyYyVPqGA8YKu8RbB1c-38.jpg
     * @param User $user
     * @param UploadedFile $file
     * @param string $type
     * @return string|null
     */
    private function generateUserImagePath(User $user, UploadedFile $file, string $type): ?string
    {
        if (!in_array($type, ['avatarImage', 'coverImage'])) {
            return null;
        }
        $basePath = config('settings.storageBasePath.'.$type);
        $date = Carbon::now()->format('Y/m/d');
        $hashString = Str::random(30);
        $fileExtension = $file->extension();
        return "{$basePath}/{$date}/{$hashString}-{$user->id}.{$fileExtension}";
    }

    /**
     * @param $params
     * @return array
     */
    public function getListFollowers($params): array
    {
        $userId = $params['userId'];
        $user = $this->findWhere([
            'id' => $userId
        ])->first();
        if ($this->isBanned($user)) {
            return [
                'message' => trans('messages.user.userBanned')
            ];
        }
        if ($this->isBlock(Auth::id(), $userId)) {
            return [
                'message' => trans('messages.user.userBlocked')
            ];
        }
        if ($userId == Auth::id() || $this->isPublic($user)
            || $this->isFollowingUser(Auth::id(), $userId)) {
            $listFollowers = $this->repository->getListFollower($userId)
                ->with(['following'])
                ->with(['blockByLoggedInUser'])
                ->paginate($params['per_page'], ['*'], 'currentPage', $params['current_page']);
            $paginateUser = $listFollowers->toArray();
            foreach ($paginateUser['data'] as &$user) {
                $user['status_follow'] = empty(!$user['following'])
                    ? $user['following']['status']: FollowingStatusEnum::NO_FOLLOW;
                $user['status_block'] = empty(!$user['block_by_logged_in_user']);
            }
            $results = $paginateUser['data'];
            $listFollowers = fractal($results, new ListFollowUserTransformer())->toArray();
            $listFollowers['current_page'] = $paginateUser['current_page'];
            $listFollowers['total_page'] = ceil($paginateUser['total'] / $params['per_page']);
            return $listFollowers;
        }
        return [
            'message' => trans('messages.user.userNotFollowing')
        ];
    }

    public function getListFollowing($params)
    {
        $userId = $params['userId'];
        $user = $this->findWhere([
            'id' => $userId
        ])->first();
        if ($this->isBanned($user)) {
            return [
                'message' => trans('messages.user.userBanned')
            ];
        }
        if ($this->isBlock(Auth::id(), $userId)) {
            return [
                'message' => trans('messages.user.userBlocked')
            ];
        }
        if ($userId == Auth::id() || $this->isPublic($user)
            || $this->isFollowingUser(Auth::id(), $userId)) {
            $listFollowing = $this->repository->getListFollowing($userId)
                ->with(['following'])
                ->with(['blockByLoggedInUser'])
                ->paginate($params['per_page'], ['*'], 'currentPage', $params['current_page']);
            $paginateUser = $listFollowing->toArray();
            foreach ($paginateUser['data'] as &$user) {
                $user['status_follow'] = empty(!$user['following'])
                    ? $user['following']['status']: FollowingStatusEnum::NO_FOLLOW;
                $user['status_block'] = empty(!$user['block_by_logged_in_user']);
            }
            $results = $paginateUser['data'];
            $listFollowing = fractal($results, new ListFollowUserTransformer())->toArray();
            $listFollowing['current_page'] = $paginateUser['current_page'];
            $listFollowing['total_page'] = ceil($paginateUser['total'] / $params['per_page']);
            return $listFollowing;
        }
        return [
            'message' => trans('messages.user.userNotFollowing')
        ];
    }
}
