<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Interfaces\Services\Comments;

interface CommentServiceInterface
{
    public function listCommentByPost($params);

    public function postComment($params);

    public function updateComment($params);

    public function likeComment($params);

    public function unlikeComment($params);

    public function deleteComment($params);
}
