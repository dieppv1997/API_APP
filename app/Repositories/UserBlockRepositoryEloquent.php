<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Repositories;

use App\Interfaces\Repositories\UserBlockRepositoryInterface;
use App\Models\UserBlock;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Enums\Users\UserBannedEnum;

/**
 * Class UserBlockRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class UserBlockRepositoryEloquent extends BaseRepository implements UserBlockRepositoryInterface
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return UserBlock::class;
    }
    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    public function findByUserIdAndBlockId($userId, $blockUserId)
    {
        return $this->model->where([
            'user_id' => $userId,
            'block_user_id' => $blockUserId,
        ]);
    }

    public function getListBlock($userId)
    {
        return $this->model->select('users.id', 'users.nickname', 'users.avatar_image')
            ->join('users', 'users.id', '=', 'user_blocks.block_user_id')
            ->where([
                'user_blocks.user_id' => $userId,
                'users.is_banned' => UserBannedEnum::USER_NOT_BANNED
            ])
            ->whereNull('users.deleted_at')
            ->orderBy('users.id', 'DESC');
    }
}
