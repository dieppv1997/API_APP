<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Repositories;

use App\Enums\Recommendation\RecommendLimitEnum;
use App\Enums\Users\FollowingStatusEnum;
use App\Enums\Users\UserBannedEnum;
use App\Enums\Users\UserPublicStatusEnum;
use App\Enums\Users\UserStatusEnum;
use App\Interfaces\Repositories\UserFollowRepositoryInterface;
use App\Models\UserFollow;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;

/**
 * Class UserFollowRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class UserFollowRepositoryEloquent extends BaseRepository implements UserFollowRepositoryInterface
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model(): string
    {
        return UserFollow::class;
    }

    /**
     * Boot up the repository, pushing criteria
     * @throws RepositoryException
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    public function getListRecommendUser($params, $checkCurrentFollow)
    {
        $currentUserId = Auth::user()->id;
        return $this->model->select('users.id', 'nickname', 'avatar_image', DB::raw('COUNT(*) as total_follow'))
            ->join('users', 'users.id', '=', 'user_follows.following_id')
            ->where([
                ['following_id', '!=', $currentUserId],
                'users.activated' => UserStatusEnum::ACTIVE,
                'status' => FollowingStatusEnum::FOLLOWING,
                'users.is_public' => UserPublicStatusEnum::PUBLIC_STATUS,
                'users.is_banned' => UserBannedEnum::USER_NOT_BANNED
            ])
            ->whereNull('users.deleted_at')
            ->whereNotIn('following_id', $checkCurrentFollow)
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('user_blocks')
                    ->where(function ($query) {
                        $query->where(function ($query) {
                            $query->whereRaw('user_blocks.block_user_id = user_follows.following_id')
                                ->where('user_blocks.user_id', '=', Auth::id());
                        })
                        ->orWhere(function ($query) {
                            $query->whereRaw('user_blocks.user_id = user_follows.following_id')
                                ->where('user_blocks.block_user_id', '=', Auth::id());
                        });
                    });
            })
            ->groupBy('following_id')
            ->orderBy('total_follow', 'DESC')
            ->limit(RecommendLimitEnum::LIMIT_RECOMMEND_USER)
            ->get();
    }

    public function checkCurrentFollow()
    {
        $currentUserId = Auth::user()->id;
        return $this->model->select('following_id')
            ->where('user_id', '=', $currentUserId)
            ->get();
    }

    public function findByUserIdAndFollowingId($userId, $followingId)
    {
        return $this->model->where([
            'user_id' => $userId,
            'following_id' => $followingId,
        ]);
    }

    public function getFollowingOfUserId($userId)
    {
        return $this->model
            ->join('users', 'users.id', '=', 'following_id')
            ->where('users.is_banned', UserBannedEnum::USER_NOT_BANNED)
            ->whereNull('users.deleted_at')
            ->where([
                'user_id' => $userId,
            ])
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('user_blocks')
                    ->where(function ($query) {
                        $query->where(function ($query) {
                            $query->whereRaw('user_blocks.block_user_id = user_follows.following_id')
                                ->where('user_blocks.user_id', '=', Auth::id());
                        })
                            ->orWhere(function ($query) {
                                $query->whereRaw('user_blocks.user_id = user_follows.following_id')
                                    ->where('user_blocks.block_user_id', '=', Auth::id());
                            });
                    });
            });
    }

    public function getFollowerOfUserId($userId)
    {
        return $this->model
            ->join('users', 'users.id', '=', 'user_id')
            ->where('users.is_banned', UserBannedEnum::USER_NOT_BANNED)
            ->whereNull('users.deleted_at')
            ->where([
                'following_id' => $userId,
            ])
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('user_blocks')
                    ->where(function ($query) {
                        $query->where(function ($query) {
                            $query->whereRaw('user_blocks.block_user_id = user_follows.user_id')
                                ->where('user_blocks.user_id', '=', Auth::id());
                        })
                            ->orWhere(function ($query) {
                                $query->whereRaw('user_blocks.user_id = user_follows.user_id')
                                    ->where('user_blocks.block_user_id', '=', Auth::id());
                            });
                    });
            });
    }

    public function updateFollowForUserPublic($userId)
    {
        return $this->model
            ->where([
                'following_id' => $userId,
                'status' => FollowingStatusEnum::WAITING
            ])
            ->update(['status' => FollowingStatusEnum::FOLLOWING]);
    }
}
