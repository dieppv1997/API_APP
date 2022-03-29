<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Services\Posts;

use App\Enums\Posts\PostStatusEnum;
use App\Exceptions\NotFoundException;
use App\Interfaces\Repositories\PostRepositoryInterface;
use App\Interfaces\Repositories\TagRepositoryInterface;
use App\Interfaces\Repositories\UserRepositoryInterface;
use App\Interfaces\Services\Posts\PostListServiceInterface;
use App\Services\BaseService;
use App\Traits\UserTrait;
use App\Transformers\Posts\CompactOfPostTransformer;
use App\Transformers\Posts\MyDraftTransformer;
use App\Transformers\Posts\MyPostTransformer;
use App\Transformers\Posts\PostFullTransformer;
use App\Transformers\Posts\PostSimpleTransformer;
use Illuminate\Support\Facades\Auth;

class PostListService extends BaseService implements PostListServiceInterface
{
    use UserTrait;

    protected $tagRepository;
    protected $userRepository;

    /**
     * PostListService constructor.
     * @param PostRepositoryInterface $repository
     * @param TagRepositoryInterface $tagRepository
     * @param UserRepositoryInterface $userRepository
     */
    public function __construct(
        PostRepositoryInterface $repository,
        TagRepositoryInterface $tagRepository,
        UserRepositoryInterface $userRepository
    ) {
        $this->repository = $repository;
        $this->tagRepository = $tagRepository;
        $this->userRepository =$userRepository;
    }

    /**
     * @param $params
     * @return array
     * @throws NotFoundException
     */
    public function listByTag($params): array
    {
        $tag = $this->tagRepository->where(['id' => $params['tagId']])->first();
        if (empty($tag)) {
            throw new NotFoundException(trans('messages.tags.tagNotFound'));
        }

        $paginateData = $this->repository->getPostsByTag($tag->id)
            ->paginate($params['per_page'], ['*'], 'currentPage', $params['current_page']);
        $paginateData = $paginateData->toArray();

        $results = $paginateData['data'];
        $return = fractal($results, new PostSimpleTransformer())->toArray();
        $return['current_page'] = $paginateData['current_page'];
        $return['total_page'] = ceil($paginateData['total'] / $params['per_page']);
        return $return;
    }

    /**
     * @param $params
     * @return array
     * @throws NotFoundException
     */
    public function listByTagFromId($params): array
    {
        $tag = $this->tagRepository->where(['id' => $params['tagId']])->first();
        if (empty($tag)) {
            throw new NotFoundException(trans('messages.tags.tagNotFound'));
        }
        $post = $this->repository->findWhere(['id' => $params['postId']])->first();
        $paginateData = $this->repository->getPostsByTagFromPost($tag->id, $post)
            ->paginate($params['per_page'], ['*'], 'currentPage', $params['current_page']);
        $paginateData = $paginateData->toArray();
        $results = $paginateData['data'];
        $return = fractal($results, new PostFullTransformer())->toArray();
        $return['current_page'] = $paginateData['current_page'];
        $return['total_page'] = ceil($paginateData['total'] / $params['per_page']);
        return $return;
    }

    /**
     * @param $params
     * @return array
     */
    public function getListNewest($params): array
    {
        $paginateData = $this->repository->getListNewest()
            ->paginate($params['per_page'], ['*'], 'currentPage', $params['current_page']);
        $paginateData = $paginateData->toArray();
        $result = $paginateData['data'];
        $return = fractal($result, new PostSimpleTransformer())->toArray();
        $return['current_page'] = $paginateData['current_page'];
        $return['total_page'] = ceil($paginateData['total'] / $params['per_page']);
        return $return;
    }

    /**
     * @param $params
     * @return array
     */
    public function listNewestFromId($params): array
    {
        $post = $this->repository->findWhere(['id' => $params['postId']])->first();
        $paginateData = $this->repository->getListNewestFromPost($post)
            ->paginate($params['per_page'], ['*'], 'currentPage', $params['current_page']);
        $paginateData = $paginateData->toArray();
        $result = $paginateData['data'];
        $return = fractal($result, new PostFullTransformer())->toArray();
        $return['current_page'] = $paginateData['current_page'];
        $return['total_page'] = ceil($paginateData['total'] / $params['per_page']);
        return $return;
    }

    /**
     * @param $params
     * @return array
     */
    public function listByFollowingUser($params): array
    {
        $currentUserId = Auth::id();
        $paginateData = $this->repository->getPostsByFollowingUser($currentUserId)
            ->paginate($params['per_page'], ['*'], 'currentPage', $params['current_page']);
        $paginateData = $paginateData->toArray();
        $result = $paginateData['data'];
        $return = fractal($result, new PostFullTransformer())->toArray();
        $return['current_page'] = $paginateData['current_page'];
        $return['total_page'] = ceil($paginateData['total'] / $params['per_page']);
        return $return;
    }

    /**
     * @param $params
     * @return array
     * @throws NotFoundException
     */
    public function listDraftByUser($params): array
    {
        $checkDraft = $this->findWhere([
            'user_id' => Auth::id(),
            'status' => PostStatusEnum::DRAFT,
        ]);
        if (!$checkDraft) {
            throw new NotFoundException(trans('exception.record_not_found'));
        }
        $listDraft = $this->repository->getDraftOfPostByUser()
            ->orderBy('posts.id', 'DESC')
            ->paginate($params['per_page'], ['*'], 'currentPage', $params['current_page'])
            ->toArray();
        $result = $listDraft['data'];
        $return = fractal($result, new MyDraftTransformer())->toArray();
        $return['current_page'] = $listDraft['current_page'];
        $return['total_page'] = ceil($listDraft['total'] / $params['per_page']);
        return $return;
    }

    /**
     * @param $params
     * @return array
     * @throws NotFoundException
     */
    public function listMyPostOfUser($params): array
    {
        $userId = $params['userId'];
        $user = $this->findUserById($userId);
        if (empty($user)) {
            throw new NotFoundException(trans('exception.not_found'));
        }
        if ($this->isBanned($user)) {
            return [
                'message' => trans('messages.user.userBanned')
            ];
        }
        if ($this->isBlock(Auth::id(), $userId)) {
            throw new NotFoundException(trans('messages.posts.not_found'));
        }
        if ($userId == Auth::id() || $this->isPublic($user)
            || $this->isFollowingUser(Auth::id(), $userId)) {
            $listMyPost = $this->repository->getPostByUserId($userId)
                ->paginate($params['per_page'], ['*'], 'currentPage', $params['current_page'])
                ->toArray();
            $result = $listMyPost['data'];
            $return = fractal($result, new MyPostTransformer())->toArray();
            $return['current_page'] = $listMyPost['current_page'];
            $return['total_page'] = ceil($listMyPost['total'] / $params['per_page']);
            return $return;
        }
        return [
            'message' => trans('messages.user.userNotFollowing')
        ];
    }

    /**
     * @param $params
     * @return array
     * @throws NotFoundException
     */
    public function listMyPostFromId($params)
    {
        $userId = $params['userId'];
        $postId = $params['postId'];
        $user = $this->findUserById($userId);
        if (empty($user)) {
            throw new NotFoundException(trans('exception.not_found'));
        }
        if ($this->isBanned($user)) {
            return [
                'message' => trans('messages.user.userBanned')
            ];
        }
        if ($this->isBlock(Auth::id(), $userId)) {
            throw new NotFoundException(trans('messages.posts.not_found'));
        }
        if ($userId == Auth::id() || $this->isPublic($user)
            || $this->isFollowingUser(Auth::id(), $userId)) {
            $post = $this->repository->findWhere(['id' => $postId])->first();
            $listMyPostFromId = $this->repository->getPostByUserFromPost($userId, $post)
                ->paginate($params['per_page'], ['*'], 'currentPage', $params['current_page'])
                ->toArray();
            $result = $listMyPostFromId['data'];
            $return = fractal($result, new PostFullTransformer())->toArray();
            $return['current_page'] = $listMyPostFromId['current_page'];
            $return['total_page'] = ceil($listMyPostFromId['total'] / $params['per_page']);
            return $return;
        }
        return [
            'message' => trans('messages.user.userNotFollowing')
        ];
    }

    /**
     * @param $params
     * @return array
     * @throws NotFoundException
     */
    public function listPostLiked($params): array
    {
        $userId = $params['userId'];
        $user = $this->findUserById($userId);
        if (empty($user)) {
            throw new NotFoundException(trans('exception.not_found'));
        }
        if ($this->isBanned($user)) {
            return [
                'message' => trans('messages.user.userBanned')
            ];
        }
        if ($this->isBlock(Auth::id(), $userId)) {
            throw new NotFoundException(trans('messages.posts.not_found'));
        }
        if ($this->isCurrentLoggedInUser($userId) || $this->isPublic($user)
            || $this->isFollowingUser(Auth::id(), $userId)) {
            $listPostLiked = $this->repository->getPostLikedOfUser($userId)
                ->paginate($params['per_page'], ['*'], 'currentPage', $params['current_page'])
                ->toArray();

            $result = $listPostLiked['data'];
            $return = fractal($result, new CompactOfPostTransformer())->toArray();
            $return['current_page'] = $listPostLiked['current_page'];
            $return['total_page'] = ceil($listPostLiked['total'] / $params['per_page']);
            return $return;
        }
        return [
            'message' => trans('messages.user.userNotFollowing')
        ];
    }

    /**
     * @param $params
     * @return array
     */
    public function listPostLikedFromPostId($params)
    {
        $userId = $params['userId'];
        $likedId = $params['likedId'];
        $user = $this->findUserById($userId);
        if (empty($user)) {
            throw new NotFoundException(trans('exception.not_found'));
        }
        if ($this->isBanned($user)) {
            return [
                'message' => trans('messages.user.userBanned')
            ];
        }
        if ($this->isBlock(Auth::id(), $userId)) {
            throw new NotFoundException(trans('messages.posts.not_found'));
        }
        if ($userId == Auth::id() || $this->isPublic($user)
            || $this->isFollowingUser(Auth::id(), $userId)) {
            $listPostLikedFromId = $this->repository->getPostLikedOfUserFromId($userId, $likedId)
                ->paginate($params['per_page'], ['*'], 'currentPage', $params['current_page'])
                ->toArray();
            $result = $listPostLikedFromId['data'];
            $return = fractal($result, new PostFullTransformer())->toArray();
            $return['current_page'] = $listPostLikedFromId['current_page'];
            $return['total_page'] = ceil($listPostLikedFromId['total'] / $params['per_page']);
            return $return;
        }
        return [
            'message' => trans('messages.user.userNotFollowing')
        ];
    }

    public function findUserById($userId)
    {
        return $this->userRepository->findWhere(['id' => $userId])->first();
    }
}
