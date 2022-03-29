<?php
namespace App\Services\Comments;

use App\Enums\Notifications\NotificationTemplateNameEnum;
use App\Enums\Posts\PostStatusEnum;
use App\Exceptions\BadRequestException;
use App\Exceptions\NotFoundException;
use App\Interfaces\Repositories\CommentLikeRepositoryInterface;
use App\Interfaces\Repositories\CommentRepositoryInterface;
use App\Interfaces\Repositories\NotificationRepositoryInterface;
use App\Interfaces\Repositories\PostRepositoryInterface;
use App\Interfaces\Services\Comments\CommentServiceInterface;
use App\Services\BaseService;
use App\Traits\BadWordTrait;
use App\Traits\FirebaseNotificationTrait;
use App\Traits\UserTrait;
use App\Transformers\Comments\CommentTransformer;
use App\Transformers\Posts\PostDetailTransformer;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CommentService extends BaseService implements CommentServiceInterface
{
    use FirebaseNotificationTrait, UserTrait, BadWordTrait;

    public $postRepository;
    public $commentLikeRepository;
    public $commentRepository;
    public $notificationRepository;

    /**
     * CommentService constructor.
     * @param CommentRepositoryInterface $repository
     * @param PostRepositoryInterface $postRepository
     * @param CommentLikeRepositoryInterface $commentLikeRepository
     * @param CommentRepositoryInterface $commentRepository
     * @param NotificationRepositoryInterface $notificationRepository
     */
    public function __construct(
        CommentRepositoryInterface $repository,
        PostRepositoryInterface $postRepository,
        CommentLikeRepositoryInterface $commentLikeRepository,
        CommentRepositoryInterface $commentRepository,
        NotificationRepositoryInterface $notificationRepository
    ) {
        $this->repository = $repository;
        $this->postRepository = $postRepository;
        $this->commentLikeRepository = $commentLikeRepository;
        $this->commentRepository = $commentRepository;
        $this->notificationRepository = $notificationRepository;
    }

    /**
     * @param $params
     * @return array
     */
    public function listCommentByPost($params): array
    {
        $this->postRepository->findOrFail($params['postId']);
        if (empty($params['parent_id'])) {
            $paginateData = $this->repository->getListRootCommentByPostId($params['postId'])
                ->paginate($params['per_page'], ['*'], 'currentPage', $params['current_page']);
        } else {
            $paginateData = $this->repository
                ->getListRepliesByCommentIdAndPostId($params['parent_id'], $params['postId'])
                ->paginate($params['per_page'], ['*'], 'currentPage', $params['current_page']);
        }
        $paginateData = $paginateData->toArray();
        $results = $paginateData['data'];
        $return = fractal($results, new CommentTransformer())->toArray();
        $return['current_page'] = $paginateData['current_page'];
        $return['total_page'] = ceil($paginateData['total'] / $params['per_page']);
        return $return;
    }

    /**
     * Post or reply comment for specific post
     * @param $params
     * @return array
     * @throws NotFoundException|GuzzleException|BadRequestException
     */
    public function postComment($params): array
    {
        $post = $this->postRepository->with(['author'])->findWhere([
            'id' => $params['postId'],
            'status' => PostStatusEnum::PUBLISHED
        ])->first();
        $badWord = $this->getBadWord();
        if ($this->isBadWord($params['content'], $badWord)) {
            throw new BadRequestException(trans('messages.NG_word'));
        }
        if ($post == null || empty($post->author) || $this->isBanned($post->author)) {
            throw new NotFoundException(trans('messages.posts.not_found'));
        }
        $userId = Auth::id();
        $authorOfPost = $post->author;
        $authorIdOfPost = $post->user_id;
        if ($userId == $authorIdOfPost || $this->isPublic($authorOfPost)
            || $this->isFollowingUser($userId, $authorIdOfPost)) {
            $insertData = [
                'post_id' => $params['postId'],
                'user_id' => Auth::user()->id,
                'content' => $params['content']
            ];
            $needPushNotification = true;
            if (!empty($params['parent_id'])) {
                $parentComment = $this->repository->with('author')
                ->findWhere([
                    'id' => $params['parent_id'],
                    'post_id' => $params['postId']
                ])
                ->first();
                if (empty($parentComment) || $this->isBanned($parentComment->author)) {
                    throw new NotFoundException(trans('exception.record_not_found'));
                }
                $insertData['parent_id'] = $params['parent_id'];
                $receiverNotification = $parentComment->user_id;
                $notificationType = NotificationTemplateNameEnum::NEW_REPLY_COMMENT;
                if ($this->isCurrentLoggedInUser($parentComment->user_id)) {
                    $needPushNotification = false;
                }
            } else {
                $receiverNotification = $post->user_id;
                $notificationType = NotificationTemplateNameEnum::NEW_COMMENT;
                if ($this->isCurrentLoggedInUser($post->user_id)) {
                    $needPushNotification = false;
                }
            }

            $newComment = $this->create($insertData);

            $detailPost = $this->postRepository->getPostById($params['postId'])->first();
            $postDetail = fractal($detailPost, new PostDetailTransformer())->toArray();

            if ($needPushNotification) {
                $notificationData = [
                    'notification_type' => $notificationType,
                    'notification_entity_id' => $newComment->id,
                    'push_entity_type' => $this->getEntityTypeByNotificationType($notificationType),
                    'push_entity_id' =>  $post->id,
                    'post_id' => $post->id
                ];
                $this->notificationHandle($receiverNotification, $notificationData);
            }

            return array_merge([
                'message' => trans('messages.comments.commentCreatedSuccessful')
            ], $postDetail);
        } else {
            throw new NotFoundException(trans('messages.posts.not_found'));
        }
    }

    /**
     * @param $params
     * @return array
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function updateComment($params): array
    {
        $post = $this->postRepository->with(['author'])->findWhere([
            'id' => $params['postId'],
            'status' => PostStatusEnum::PUBLISHED
        ])->first();
        $badWord = $this->getBadWord();
        if ($this->isBadWord($params['content'], $badWord)) {
            throw new BadRequestException(trans('messages.NG_word'));
        }
        if ($post == null || empty($post->author) || $this->isBanned($post->author)) {
            throw new NotFoundException(trans('messages.posts.not_found'));
        }
        $userId = Auth::id();
        $authorOfPost = $post->author;
        $authorIdOfPost = $post->user_id;
        if ($userId == $authorIdOfPost || $this->isPublic($authorOfPost)
            || $this->isFollowingUser($userId, $authorIdOfPost)) {
            $comment = $this->repository->findWhere([
                'id' => $params['commentId'],
                'user_id' => Auth::user()->id,
            ])->first();
            if (empty($comment)) {
                throw new BadRequestException(trans('exception.record_not_found'));
            }
            $this->update(['content' => $params['content']], $comment->id);
            return [
                'message' => trans('messages.comments.commentUpdatedSuccessful')
            ];
        } else {
            throw new NotFoundException(trans('messages.posts.not_found'));
        }
    }

    /**
     * @param $params
     * @return array
     * @throws GuzzleException
     * @throws NotFoundException
     */
    public function likeComment($params): array
    {
        $userId = Auth::user()->id;
        $comment = $this->repository->with(['post', 'author'])
        ->findWhere([
            'id' => $params['commentId']
        ])
        ->first();
        if ($comment == null || empty($comment->author) || $this->isBanned($comment->author)) {
            throw new NotFoundException(trans('exception.record_not_found'));
        }
        $post = $this->postRepository->with(['author'])->findWhere([
            'id' => $comment->post_id,
            'status' => PostStatusEnum::PUBLISHED
        ])->first();
        if ($post == null || empty($post->author) || $this->isBanned($post->author)) {
            throw new NotFoundException(trans('messages.posts.not_found'));
        }
        $authorOfPost = $post->author;
        $authorIdOfPost = $post->user_id;
        if ($userId == $authorIdOfPost || $this->isPublic($authorOfPost)
            || $this->isFollowingUser($userId, $authorIdOfPost)) {
            if ($this->hasBlockRelation($userId, $comment->author->id)) {
                throw new NotFoundException(trans('exception.record_not_found'));
            }
            if (empty($comment->post)) {
                throw new NotFoundException(trans('messages.posts.not_found'));
            }

            $commentLike = $this->commentLikeRepository->findWhere([
                'comment_id' => $params['commentId'],
                'user_id' => $userId
            ])->first();

            if ($commentLike) {
                return [
                    'message' => trans('messages.comments.likeSuccessful')
                ];
            }
            $this->commentLikeRepository->create([
                'comment_id' => $params['commentId'],
                'user_id' => $userId,
            ]);
            if (!$this->isCurrentLoggedInUser($comment->user_id)) {
                $notificationType = NotificationTemplateNameEnum::LIKE_COMMENT;
                $notificationData = [
                    'notification_type' => $notificationType,
                    'notification_entity_id' => $params['commentId'],
                    'push_entity_type' => $this->getEntityTypeByNotificationType($notificationType),
                    'push_entity_id' => $post->id,
                    'post_id' => $post->id
                ];
                $this->notificationHandle($comment->user_id, $notificationData);
            }
            return [
                'message' => trans('messages.comments.likeSuccessful')
            ];
        } else {
            throw new NotFoundException(trans('messages.posts.not_found'));
        }
    }

    /**
     * @param $params
     * @return array
     * @throws NotFoundException
     */
    public function unlikeComment($params): array
    {
        $userId = Auth::user()->id;
        $comment = $this->repository->findWhere([
            'id' => $params['commentId']
        ])->first();
        if ($comment == null || empty($comment->author) || $this->isBanned($comment->author)) {
            throw new NotFoundException(trans('exception.record_not_found'));
        }
        $post = $this->postRepository->with(['author'])->findWhere([
            'id' => $comment->post_id,
            'status' => PostStatusEnum::PUBLISHED
        ])->first();
        if ($post == null || empty($post->author) || $this->isBanned($post->author)) {
            throw new NotFoundException(trans('messages.posts.not_found'));
        }
        $authorOfPost = $post->author;
        $authorIdOfPost = $post->user_id;
        if ($userId == $authorIdOfPost || $this->isPublic($authorOfPost)
            || $this->isFollowingUser($userId, $authorIdOfPost)) {
            if ($this->hasBlockRelation($userId, $comment->author->id)) {
                throw new NotFoundException(trans('exception.record_not_found'));
            }
            $commentLike = $this->commentLikeRepository->findWhere([
                'comment_id' => $params['commentId'],
                'user_id' => $userId
            ])->first();

            if (!$commentLike) {
                return [
                    'message' => trans('messages.comments.unlikeSuccessful')
                ];
            }
            $this->commentLikeRepository->deleteWhere([
                'comment_id' => $params['commentId'],
                'user_id' => $userId,
            ]);
            return [
                'message' => trans('messages.comments.unlikeSuccessful')
            ];
        } else {
            throw new NotFoundException(trans('messages.posts.not_found'));
        }
    }

    /**
     * @param $params
     * @return array
     * @throws NotFoundException
     */
    public function deleteComment($params): array
    {
        $post = $this->postRepository->getPostWithAuthorById($params['postId']);
        if ($post == null || empty($post->author) || $this->isBanned($post->author)) {
            throw new NotFoundException(trans('messages.posts.not_found'));
        }
        $userId = Auth::id();
        $authorOfPost = $post->author;
        $authorIdOfPost = $post->user_id;
        if ($userId == $authorIdOfPost || $this->isPublic($authorOfPost)
            || $this->isFollowingUser($userId, $authorIdOfPost)) {
            $comment = $this->repository->findWhere([
                'id' => $params['commentId'],
                'user_id' => Auth::user()->id,
                'post_id' => $params['postId']
            ])->first();

            if (empty($comment)) {
                throw new NotFoundException(trans('exception.record_not_found'));
            }
            $this->deleteWhere([
                'id' => $params['commentId'],
            ]);
            if (empty($comment['parent_id'])) {
                $this->commentRepository->deleteWhere(['parent_id' => $params['commentId']]);
            }
            $detailPost = $this->postRepository->getPostById($params['postId'])->first();
            $postDetail = fractal($detailPost, new PostDetailTransformer())->toArray();
            return array_merge([
                'message' => trans('messages.comments.commentDeletedSuccessful')
            ], $postDetail);
        } else {
            throw new NotFoundException(trans('messages.posts.not_found'));
        }
    }
}
