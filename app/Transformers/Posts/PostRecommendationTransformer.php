<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Transformers\Posts;

use App\Helpers\Helper;
use Illuminate\Support\Facades\Auth;
use League\Fractal\TransformerAbstract;

class PostRecommendationTransformer extends TransformerAbstract
{
    /**
     * @param array $post
     * @return array
     */
    public function transform(array $post): array
    {
        return [
            'id' => $post['id'],
            'thumbnail' => Helper::generateImageUrl($post['thumbnail']),
            'owner_by_current_user' => $post['user_id'] == Auth::id(),
        ];
    }
}
