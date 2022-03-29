<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Transformers\Tags;

use App\Models\Tag;
use League\Fractal\TransformerAbstract;

class SearchTagTransformer extends TransformerAbstract
{
    /**
     * @param array $post
     * @return array
     */
    public function transform(Tag $tag)
    {
        return [
            'id' => $tag['id'],
            'name' => $tag['name']
        ];
    }
}
