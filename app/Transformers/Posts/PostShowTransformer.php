<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Transformers\Posts;

use App\Helpers\Helper;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use League\Fractal\TransformerAbstract;

class PostShowTransformer extends TransformerAbstract
{
    /**
     * @param Post $post
     * @return array
     */
    public function transform(Post $post): array
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
            'place' => $this->generatePlace($post),
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
            'place_id' => $post['place']['google_place_id'],
            'place_name' => $post['place']['place_name'],
            'place_address' => $post['place']['place_address'],
            'latitude' => $post['place']['latitude'],
            'longitude' => $post['place']['longitude'],
        ] : null;
    }
}
