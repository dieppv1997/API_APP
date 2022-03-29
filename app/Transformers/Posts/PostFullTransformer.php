<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Transformers\Posts;

use App\Helpers\Helper;
use Illuminate\Support\Facades\Auth;
use League\Fractal\TransformerAbstract;

class PostFullTransformer extends TransformerAbstract
{
    /**
     * @param array $post
     * @return array
     */
    public function transform(array $post): array
    {
        $tagList = [];
        foreach ($post['tags'] as $item) {
            $tagList[] = [
                'tag_id' => $item['id'],
                'tag_name' => $item['name']
            ];
        }
        return [
            'id' => $post['id'],
            'caption' => $post['caption'],
            'post_image' => Helper::generateImageUrl($post['image']),
            'posted_at' => Helper::postedDateFormat($post['published_at']),
            'place' => $this->generatePlace($post),
            'author' => [
                'author_id' => $post['user_id'],
                'author_name' => $post['nickname'],
                'author_avatar' => Helper::generateImageUrl($post['avatar_image']),
            ],
            'like_count' => Helper::countFormat($post['likes_count']),
            'comment_count' => Helper::countFormat($post['post_comments_count']),
            'liked' => !empty($post['liked']),
            'tags' => $tagList,
            'owner_by_current_user' => $post['user_id'] == Auth::id(),
        ];
    }

    /**
     * @param $post
     * @return array|null
     */
    private function generatePlace($post)
    {
        return !empty($post['place_id']) ? [
            'place_id' => $post['google_place_id'],
            'place_name' => $post['place_name'],
            'place_address' => $post['place_address'],
            'latitude' => $post['latitude'],
            'longitude' => $post['longitude'],
        ] : null;
    }
}
