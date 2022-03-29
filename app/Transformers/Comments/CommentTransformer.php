<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Transformers\Comments;

use App\Helpers\Helper;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;
use League\Fractal\TransformerAbstract;

class CommentTransformer extends TransformerAbstract
{
    /**
     * @param Comment|array $comment
     * @return array
     */
    public function transform($comment): array
    {
        $replies = [];
        if ($comment['parent_id'] == 0) {
            foreach ($comment['replies'] as $reply) {
                $replies[] = $this->generateComment($reply);
            }
        }

        $item = $this->generateComment($comment);
        return array_merge($item, ['replies' => $replies]);
    }

    private function generateComment(array $comment): array
    {
        return [
            'id' => $comment['id'],
            'content' => $comment['content'],
            'commented_at' => Helper::postedDateFormat($comment['created_at']),
            'author' => [
                'author_id' => $comment['author']['id'],
                'author_nickname' => $comment['author']['nickname'],
                'author_avatar' => Helper::generateImageUrl($comment['author']['avatar_image']),
            ],
            'liked' => !empty($comment['liked']),
            'owner_by_current_user' => $comment['author']['id'] == Auth::id(),
            'like_count' => Helper::countFormat($comment['like_count']),
        ];
    }
}
