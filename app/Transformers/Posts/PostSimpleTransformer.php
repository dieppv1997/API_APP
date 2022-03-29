<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Transformers\Posts;

use App\Helpers\Helper;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use League\Fractal\TransformerAbstract;

class PostSimpleTransformer extends TransformerAbstract
{
    /**
     * @param Post $post
     * @return array
     */
    public function transform($post): array
    {
        return [
            'id' => $post['id'],
            'post_image' => Helper::generateImageUrl($post['image']),
            'owner_by_current_user' => $post['user_id'] == Auth::id(),
        ];
    }
}
