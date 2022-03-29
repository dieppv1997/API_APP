<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Interfaces\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface CommentRepositoryInterface.
 *
 * @package namespace App\Interfaces;
 */
interface CommentRepositoryInterface extends RepositoryInterface
{
    public function getListRootCommentByPostId(int $postId);

    public function getListRepliesByCommentIdAndPostId(int $commentId, int $postId);
}
