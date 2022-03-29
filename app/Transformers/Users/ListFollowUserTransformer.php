<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Transformers\Users;

use App\Helpers\Helper;
use App\Models\User;
use League\Fractal\TransformerAbstract;

class ListFollowUserTransformer extends TransformerAbstract
{
    /**
     * @param User $user
     * @return array
     */
    public function transform($user): array
    {
        return [
            'id' => $user['id'],
            'nickname' => $user['nickname'],
            'is_public' => $user['is_public'],
            'avatar_image' => Helper::generateImageUrl($user['avatar_image']),
            'status_follow' => $user['status_follow'],
            'block_user' => $user['status_block'],
        ];
    }
}
