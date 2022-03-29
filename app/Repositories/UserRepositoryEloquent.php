<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Repositories;

use App\Enums\Users\FollowingStatusEnum;
use App\Enums\Users\RegisterTypeEnum;
use App\Enums\Users\UserBannedEnum;
use App\Enums\Users\UserStatusEnum;
use App\Interfaces\Repositories\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class UserRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class UserRepositoryEloquent extends BaseRepository implements UserRepositoryInterface
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return User::class;
    }
    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    public function searchNickNameByKeySearch($keySearch)
    {
        $orderRaw = "CASE WHEN nickname = '?' then 1
                WHEN nickname like '?%' then 2
                WHEN nickname like '%?%' then 3
                WHEN nickname like '%?' then 4
                END";
        return $this->model->select('id', 'nickname', 'is_public', 'avatar_image')
            ->where('nickname', 'like', '%'.$keySearch.'%')
            ->where('deleted_at', null)
            ->where('is_banned', UserBannedEnum::USER_NOT_BANNED)
            ->where(function ($query) {
                $query->where('activated', UserStatusEnum::ACTIVE)
                    ->orWhere('register_type', RegisterTypeEnum::REGISTER_NICKNAME);
            })
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('user_blocks')
                    ->whereRaw('user_blocks.user_id = users.id')
                    ->where('user_blocks.block_user_id', '=', Auth::id());
            })
            ->orderByRaw($orderRaw, [$keySearch, $keySearch, $keySearch, $keySearch])
            ->orderBy('id', 'DESC');
    }

    public function getById($id)
    {
        return $this->model->where('id', $id);
    }

    /**
     * @param $userId
     * @return mixed
     */
    public function getListFollower($userId)
    {
        return $this->model->select('users.id', 'users.nickname', 'users.avatar_image', 'users.is_public')
            ->join('user_follows', 'users.id', '=', 'user_follows.user_id')
            ->where([
                'following_id' => $userId,
                'is_banned' => UserBannedEnum::USER_NOT_BANNED,
                ['status', '!=', FollowingStatusEnum::NO_FOLLOW],
            ])
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('user_blocks')
                    ->whereRaw('user_blocks.user_id = users.id')
                    ->where('user_blocks.block_user_id', '=', Auth::id());
            })
            ->orderBy('users.id', 'DESC');
    }

    public function getListFollowing($userId)
    {
        return $this->model->select('users.id', 'users.nickname', 'users.avatar_image', 'users.is_public')
            ->join('user_follows', 'users.id', '=', 'user_follows.following_id')
            ->where([
                'user_id' => $userId,
                ['status', '!=', FollowingStatusEnum::NO_FOLLOW],
                'is_banned' => UserBannedEnum::USER_NOT_BANNED,
            ])
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('user_blocks')
                    ->whereRaw('user_blocks.user_id = users.id')
                    ->where('user_blocks.block_user_id', '=', Auth::id());
            })
            ->orderBy('users.id', 'DESC');
    }
}
