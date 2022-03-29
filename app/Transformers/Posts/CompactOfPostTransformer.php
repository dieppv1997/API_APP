<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Transformers\Posts;

use App\Helpers\Helper;
use League\Fractal\TransformerAbstract;

class CompactOfPostTransformer extends TransformerAbstract
{
    /**
     * @param array $post
     * @return array
     */
    public function transform(array $post): array
    {
        return [
            'id' => $post['id'],
            'liked_id' => $post['liked_id'],
            'caption' => $post['caption'],
            'post_image' => Helper::generateImageUrl($post['image']),
            'posted_at' => Helper::postedDateFormat($post['published_at']),
        ];
    }
}
