<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Services\Block;

use App\Exceptions\BadRequestException;
use App\Interfaces\Repositories\UserBlockRepositoryInterface;
use App\Interfaces\Repositories\UserRepositoryInterface;
use App\Interfaces\Repositories\UserFollowRepositoryInterface;
use App\Interfaces\Services\Block\BlockServiceInterface;
use App\Services\BaseService;
use App\Traits\UserTrait;
use App\Transformers\Users\ListBlockUserTransformer;
use Illuminate\Support\Facades\Auth;

class BlockService extends BaseService implements BlockServiceInterface
{
    use UserTrait;

    public $repository;
    public $userRepository;
    public $userFollowRepository;

    public function __construct(
        UserBlockRepositoryInterface $repository,
        UserRepositoryInterface $userRepository,
        UserFollowRepositoryInterface $userFollowRepository
    ) {
        $this->repository = $repository;
        $this->userRepository = $userRepository;
        $this->userFollowRepository = $userFollowRepository;
    }

    /**
     * @param $params
     * @return array
     * @throws BadRequestException
     */
    public function blockUser($params)
    {
        $currentUserId = Auth::id();
        $userIdToBlock = $params['userId'];
        if ($currentUserId == $userIdToBlock) {
            throw new BadRequestException(trans('exception.bad_request'));
        }
        $blockUser = $this->userRepository
            ->notBanned()
            ->available()
            ->where(['id' => $userIdToBlock])
            ->first();
        if (empty($blockUser)) {
            throw new BadRequestException(trans('messages.user.not_found'));
        }
        $exists = $this->repository->findByUserIdAndBlockId($currentUserId, $userIdToBlock)->first();
        if (!empty($exists)) {
            throw new BadRequestException(trans('exception.bad_request'));
        }
        $this->create([
            'user_id' => $currentUserId,
            'block_user_id' => $userIdToBlock
        ]);
        $this->userFollowRepository->deleteWhere([
            'user_id' => $currentUserId,
            'following_id' => $userIdToBlock
        ]);
        $this->userFollowRepository->deleteWhere([
            'user_id' => $userIdToBlock,
            'following_id' => $currentUserId
        ]);
        return [
            'message' => trans('messages.user.blockSuccessful', [
                'username' => $blockUser->nickname
            ])
        ];
    }

    /**
     * @param $params
     * @return array
     * @throws BadRequestException
     */
    public function unBlockUser($params)
    {
        $currentUserId = Auth::id();
        $userIdToUnBlock = $params['userId'];
        $blockUser = $this->userRepository
            ->notBanned()
            ->available()
            ->where(['id' => $userIdToUnBlock])
            ->first();
        if (empty($blockUser)) {
            throw new BadRequestException(trans('messages.user.not_found'));
        }
        $exists = $this->repository->findByUserIdAndBlockId($currentUserId, $userIdToUnBlock)->first();
        if (empty($exists)) {
            throw new BadRequestException(trans('exception.bad_request'));
        }
        $this->delete($exists->id);
        return [
            'message' => trans('messages.user.unBlockSuccessful', [
                'username' => $blockUser->nickname
            ])
        ];
    }

    public function getListBlocked($params)
    {
        $userId = Auth::id();
        $listBlock = $this->repository->getListBlock($userId)
            ->paginate($params['per_page'], ['*'], 'currentPage', $params['current_page']);
        $paginateUser = $listBlock->toArray();
        $results = $paginateUser['data'];
        $listBlock = fractal($results, new ListBlockUserTransformer())->toArray();
        $listBlock['current_page'] = $paginateUser['current_page'];
        $listBlock['total_page'] = ceil($paginateUser['total'] / $params['per_page']);
        return $listBlock;
    }
}
