<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Services\Search;

use App\Enums\Users\FollowingStatusEnum;
use App\Enums\Users\UserLimitOfSearch;
use App\Interfaces\Repositories\PostRepositoryInterface;
use App\Interfaces\Repositories\PostTagRepositoryInterface;
use App\Interfaces\Repositories\TagRepositoryInterface;
use App\Interfaces\Repositories\UserFollowRepositoryInterface;
use App\Interfaces\Repositories\UserRepositoryInterface;
use App\Interfaces\Services\Search\SearchServiceInterface;
use App\Services\BaseService;
use App\Transformers\Posts\PostFullTransformer;
use App\Transformers\Posts\PostSimpleTransformer;
use App\Transformers\Users\SearchUserTransformer;

class SearchService extends BaseService implements SearchServiceInterface
{
    public $tagRepository;
    protected $userRepository;
    public $userFollowRepository;
    public $postRepository;
    public $postTagRepository;

    public function __construct(
        TagRepositoryInterface $tagRepositoryEloquent,
        UserRepositoryInterface $userRepository,
        UserFollowRepositoryInterface $userFollowRepository,
        PostRepositoryInterface $postRepository,
        PostTagRepositoryInterface $postTagRepository
    ) {
        $this->tagRepository = $tagRepositoryEloquent;
        $this->userRepository = $userRepository;
        $this->userFollowRepository = $userFollowRepository;
        $this->postRepository = $postRepository;
        $this->postTagRepository = $postTagRepository;
    }

    /**
     * @param $params
     * @return array
     */
    public function searchAllByKey($params): array
    {
        $keySearch = $params['keyword'];
        if (!$keySearch) {
            $users = [
                'data' => [],
            ];
            $posts = [
                'data' => [],
            ];
            $posts['current_page'] = 1;
            $posts['total_page'] = [];
            return [
                'users' => $users,
                'posts' => $posts
            ];
        }
        $users = $this->userRepository->searchNickNameByKeySearch($keySearch)
            ->with(['following', 'blockByLoggedInUser'])
            ->limit(UserLimitOfSearch::LIMIT_USER_SEARCH)
            ->get()
            ->toArray();
        foreach ($users as &$user) {
            $user['status_follow'] = empty(!$user['following'])
                ? $user['following']['status']: FollowingStatusEnum::NO_FOLLOW;
            $user['status_block'] = empty(!$user['block_by_logged_in_user']);
        }
        $users = fractal($users, new SearchUserTransformer())->toArray();

        $tags = $this->tagRepository->searchTagByName($keySearch)->pluck('id')->toArray();
        $posts = $this->postRepository->getPostsByTag($tags)
            ->paginate($params['per_page'], ['*'], 'currentPage', $params['current_page']);
        $paginatePost = $posts->toArray();
        $results = $paginatePost['data'];
        $posts = fractal($results, new PostSimpleTransformer())->toArray();
        $posts['current_page'] = $paginatePost['current_page'];
        $posts['total_page'] = ceil($paginatePost['total'] / $params['per_page']);
        return [
            'users' => $users,
            'posts' => $posts
        ];
    }

    /**
     * @param $params
     * @return array
     */
    public function searchAllUserByKey($params): array
    {
        $keySearch = $params['keyword'];
        if (!$keySearch) {
            return [
                'data' => [],
                'current_page' => 1,
                'total_page' => 0
            ];
        }
        $users = $this->userRepository->searchNickNameByKeySearch($keySearch)
            ->with(['following', 'blockByLoggedInUser'])
            ->paginate($params['per_page'], ['*'], 'currentPage', $params['current_page']);
        $paginateUser = $users->toArray();
        foreach ($paginateUser['data'] as &$user) {
            $user['status_follow'] = empty(!$user['following'])
                ? $user['following']['status']: FollowingStatusEnum::NO_FOLLOW;
            $user['status_block'] = empty(!$user['block_by_logged_in_user']);
        }
        $results = $paginateUser['data'];
        $users = fractal($results, new SearchUserTransformer())->toArray();
        $users['current_page'] = $paginateUser['current_page'];
        $users['total_page'] = ceil($paginateUser['total'] / $params['per_page']);

        return $users;
    }

    public function searchAllPostByKey($params): array
    {
        if (!$params['keyword']) {
            return [
                'data' => [],
                'current_page' => 1,
                'total_page' => 0
            ];
        }
        $keySearch = $params['keyword'];
        $tags = $this->tagRepository->searchTagByName($keySearch)->pluck('id')->toArray();
        $post = $this->postRepository->findWhere(['id' => $params['postId']])->first();
        $paginatePost = $this->postRepository->getPostsByTagFromPost($tags, $post)
            ->paginate($params['per_page'], ['*'], 'currentPage', $params['current_page'])->toArray();
        $results = $paginatePost['data'];
        $return = fractal($results, new PostFullTransformer())->toArray();
        $return['current_page'] = $paginatePost['current_page'];
        $return['total_page'] = ceil($paginatePost['total'] / $params['per_page']);
        return $return;
    }
}
