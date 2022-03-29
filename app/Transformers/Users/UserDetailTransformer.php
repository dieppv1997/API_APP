<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Transformers\Users;

use App\Helpers\Helper;
use App\Models\User;
use League\Fractal\TransformerAbstract;

class UserDetailTransformer extends TransformerAbstract
{
    /**
     * @param User $user
     * @return array
     */
    public function transform(User $user): array
    {
        return [
            'id' => $user['id'],
            'nickname' => $user['nickname'],
            'email' => $user['email'],
            'avatar_image' => Helper::generateImageUrl($user['avatar_image']),
            'cover_image' => Helper::generateImageUrl($user['cover_image']),
            'birthday' => Helper::birthdayFormat($user['birthday']),
            'gender' => Helper::genderFormat($user['gender']),
            'favorite_shop' => $user['favorite_shop'],
            'favorite_place' => $user['favorite_place'],
            'intro' => $user['intro'],
            'is_public' => !empty($user['is_public']),
            'is_banned' => !empty($user['is_banned']),
            'following_count' => $user['following_count'],
            'follower_count' => $user['follower_count'],
            'province_id' => !empty($user['province']['id']) ? $user['province']['id'] : null,
            'province' => !empty($user['province']['name']) ? $user['province']['name'] : null,
            'created_at' => Helper::dateFormat($user['created_at']),
        ];
    }
}
