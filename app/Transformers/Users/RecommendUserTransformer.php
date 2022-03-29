<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Transformers\Users;

use App\Enums\Users\FollowingStatusEnum;
use App\Helpers\Helper;
use App\Models\UserFollow;
use League\Fractal\TransformerAbstract;

class RecommendUserTransformer extends TransformerAbstract
{
    /**
     * @param UserFollow $userFollow
     * @return array
     */
    public function transform(UserFollow $userFollow)
    {
        return [
            'id' => $userFollow['id'],
            'nickname' => $userFollow['nickname'],
            'avatar_image' => Helper::generateImageUrl($userFollow['avatar_image']),
            'status_follow' => FollowingStatusEnum::NO_FOLLOW,
        ];
    }
}
