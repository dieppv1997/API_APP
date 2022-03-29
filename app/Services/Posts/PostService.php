<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Services\Posts;

use App\Enums\Notifications\NotificationTemplateNameEnum;
use App\Enums\Posts\PostStatusEnum;
use App\Enums\Posts\PostUpdateStatusEnum;
use App\Enums\Tags\TagHandleModeEnum;
use App\Exceptions\BadRequestException;
use App\Exceptions\NotFoundException;
use App\Interfaces\Repositories\CommentRepositoryInterface;
use App\Interfaces\Repositories\NotificationRepositoryInterface;
use App\Interfaces\Repositories\PostLikeRepositoryInterface;
use App\Interfaces\Repositories\PostPublishedSequenceRepositoryInterface;
use App\Interfaces\Repositories\PostRepositoryInterface;
use App\Interfaces\Repositories\PostTagRepositoryInterface;
use App\Interfaces\Repositories\TagRepositoryInterface;
use App\Interfaces\Repositories\PlaceRepositoryInterface;
use App\Interfaces\Services\Posts\PostServiceInterface;
use App\Models\Post;
use App\Services\BaseService;
use App\Traits\BadWordTrait;
use App\Traits\FirebaseNotificationTrait;
use App\Traits\UserTrait;
use App\Transformers\Posts\PostDetailTransformer;
use App\Transformers\Posts\PostShowTransformer;
use Carbon\Carbon;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Fractal\Fractal;

class PostService extends BaseService implements PostServiceInterface
{
    use FirebaseNotificationTrait, UserTrait, BadWordTrait;

    protected $tagRepository;
    protected $postLikeRepository;
    protected $postTagRepository;
    protected $placeRepository;
    protected $commentRepository;
    protected $postPublishedSequenceRepository;
    protected $notificationRepository;

    /**
     * @param PostRepositoryInterface $repository
     * @param PostLikeRepositoryInterface $postLikeRepository
     * @param TagRepositoryInterface $tagRepository
     * @param PostTagRepositoryInterface $postTagRepository
     * @param PlaceRepositoryInterface $placeRepository
     * @param CommentRepositoryInterface $commentRepository
     * @param PostPublishedSequenceRepositoryInterface $postPublishedSequenceRepository
     * @param NotificationRepositoryInterface $notificationRepository
     */
    public function __construct(
        PostRepositoryInterface $repository,
        PostLikeRepositoryInterface $postLikeRepository,
        TagRepositoryInterface $tagRepository,
        PostTagRepositoryInterface $postTagRepository,
        PlaceRepositoryInterface $placeRepository,
        CommentRepositoryInterface $commentRepository,
        PostPublishedSequenceRepositoryInterface $postPublishedSequenceRepository,
        NotificationRepositoryInterface $notificationRepository
    ) {
        $this->repository = $repository;
        $this->postLikeRepository = $postLikeRepository;
        $this->tagRepository = $tagRepository;
        $this->postTagRepository = $postTagRepository;
        $this->placeRepository = $placeRepository;
        $this->commentRepository = $commentRepository;
        $this->postPublishedSequenceRepository = $postPublishedSequenceRepository;
        $this->notificationRepository = $notificationRepository;
    }

    /**
     * @throws NotFoundException
     */
    public function getDetail($params): Fractal
    {
        $postId = $params['postId'];
        $detailPost = $this->repository->getPostById($postId, false)->first();
        if (!$detailPost) {
            throw new NotFoundException(trans('exception.record_not_found'));
        }
        return fractal($detailPost, new PostDetailTransformer());
    }

    /**
     * @param $params
     * @return array
     * @throws NotFoundException
     * @throws GuzzleException
     */
    public function likePost($params): array
    {
        $userId = Auth::user()->id;
        $post = $this->repository->with(['author'])->findWhere([
            'id' => $params['postId'],
            'status' => PostStatusEnum::PUBLISHED
        ])->first();
        if ($post == null || empty($post->author) || $this->isBanned($post->author)) {
            throw new NotFoundException(trans('messages.posts.not_found'));
        }
        $authorIdOfPost = $post->user_id;
        $authorOfPost = $post->author;
        if ($userId == $authorIdOfPost || $this->isPublic($authorOfPost)
            || $this->isFollowingUser($userId, $authorIdOfPost)) {
            $likePost = $this->postLikeRepository->findWhere([
                'post_id' => $params['postId'],
                'user_id' => $userId
            ])->first();
            if (empty($likePost)) {
                $this->postLikeRepository->create([
                    'post_id' => $params['postId'],
                    'user_id' => $userId,
                ]);

                if (!$this->isCurrentLoggedInUser($post->user_id)) {
                    $notificationType = NotificationTemplateNameEnum::LIKE_POST;
                    $notificationData = [
                        'notification_type' => $notificationType,
                        'notification_entity_id' => $post->id,
                        'push_entity_type' => $this->getEntityTypeByNotificationType($notificationType),
                        'push_entity_id' => $post->id,
                        'post_id' => $post->id,
                    ];
                    $this->notificationHandle($post->user_id, $notificationData);
                }
            }
            $postDetail = $this->getDetail($params)->toArray();
            return array_merge([
                'message' => trans('messages.posts.likeSuccessful')
            ], $postDetail);
        } else {
            throw new NotFoundException(trans('messages.posts.not_found'));
        }
    }

    /**
     * @param $params
     * @return array
     * @throws NotFoundException
     */
    public function unlikePost($params): array
    {
        $userId = Auth::user()->id;
        $post = $this->repository->getPostWithAuthorById($params['postId']);
        if ($post == null || empty($post->author) || $this->isBanned($post->author)) {
            throw new NotFoundException(trans('messages.posts.not_found'));
        }
        $authorIdOfPost = $post->user_id;
        $authorOfPost = $post->author;
        if ($userId == $authorIdOfPost || $this->isPublic($authorOfPost)
            || $this->isFollowingUser($userId, $authorIdOfPost)) {
            $likePost = $this->postLikeRepository->findWhere([
                'post_id' => $params['postId'],
                'user_id' => $userId
            ])->first();
            if (!empty($likePost)) {
                $this->postLikeRepository->deleteWhere([
                    'post_id' => $params['postId'],
                    'user_id' => $userId,
                ]);
            }
            $postDetail = $this->getDetail($params)->toArray();
            return array_merge([
                'message' => trans('messages.posts.unlikeSuccessful')
            ], $postDetail);
        } else {
            throw new NotFoundException(trans('messages.posts.not_found'));
        }
    }

    /**
     * @param $params
     * @return array
     * @throws BadRequestException
     */
    public function createPost($params): array
    {
        $postData = [
            'image' => 'temp',
            'caption' => $params['caption'],
            'user_id' => Auth::id(),
            'status' => $params['type']
        ];
        $badWord = $this->getBadWord();
        if ($params['type'] == PostStatusEnum::PUBLISHED) {
            $postData['published_at'] = Carbon::now();
        }
        if (isset($params['caption'])) {
            if ($this->isBadWord($params['caption'], $badWord)) {
                throw new BadRequestException(trans('messages.NG_word'));
            }
        }
        if (isset($params['tags'])) {
            $tags = $params['tags'];
            foreach ($tags as $tag) {
                if ($this->isBadWord($tag, $badWord)) {
                    throw new BadRequestException(trans('messages.NG_word'));
                }
            }
        }
        $newPost = $this->create($postData);
        $newPost->image = $this->generatePostImagePath($newPost, $params['image']);
        $newPost->original_image = $this->generatePostImagePath($newPost, $params['original_image'], true);
        if (!empty($params['place'])) {
            $newPost->place_id = $this->getPlaceId($params['place']);
        }
        $newPost->save();
        if ($params['type'] == PostStatusEnum::PUBLISHED) {
            $this->postPublishedSequenceRepository->create(['post_id' => $newPost->id]);
        }
        if (!empty($params['tags'])) {
            $this->postTagHandle($newPost, $params['tags'], TagHandleModeEnum::CREATE_POST);
        }
        return $newPost->toArray();
    }

    public function getPlaceId($placeData)
    {
        $placeRecord = $this->placeRepository->findWhere([
            'google_place_id' => $placeData['place_id'],
            'place_name' => $placeData['place_name'],
            'place_address' => $placeData['place_address'],
        ])->first();
        if (empty($placeRecord)) {
            $placeRecord = $this->placeRepository->create([
                'google_place_id' => $placeData['place_id'],
                'place_name' => $placeData['place_name'],
                'place_address' => $placeData['place_address'],
                'latitude' => $placeData['latitude'],
                'longitude' => $placeData['longitude'],
            ]);
        }
        return $placeRecord->id;
    }

    /**
     * @param $params
     * @param $mode
     * @return string[]
     * @throws NotFoundException|BadRequestException
     */
    public function updatePost($params, $mode): array
    {
        $post = $this->repository->findWhere([
            'id' => $params['postId'],
            'user_id' => Auth::id()
        ])->first();
        if (empty($post)) {
            throw new NotFoundException(trans('messages.posts.not_found'));
        }
        if ($mode == PostUpdateStatusEnum::PUBLISHED && $post->status != PostStatusEnum::DRAFT) {
            throw new NotFoundException(trans('messages.posts.not_found'));
        }
        $badWord = $this->getBadWord();
        if (isset($params['caption'])) {
            if ($this->isBadWord($params['caption'], $badWord)) {
                throw new BadRequestException(trans('messages.NG_word'));
            }
        }
        if (isset($params['tags'])) {
            $tags = $params['tags'];
            foreach ($tags as $tag) {
                if ($this->isBadWord($tag, $badWord)) {
                    throw new BadRequestException(trans('messages.NG_word'));
                }
            }
        }
        $updateData = [
            'caption' => $params['caption'],
        ];
        if ($mode == PostUpdateStatusEnum::PUBLISHED) {
            $updateData['status'] = PostStatusEnum::PUBLISHED;
            $updateData['published_at'] = Carbon::now();
            $this->postPublishedSequenceRepository->create(['post_id' => $post->id]);
        }

        $oldImage = $post->image;
        $oldOriginalImage = $post->original_image;
        $changeImage = !empty($params['image']);
        if ($changeImage) {
            $updateData['image'] = $this->generatePostImagePath($post, $params['image']);
            $post->image = $updateData['image'];
            $updateData['original_image'] = $this->generatePostImagePath($post, $params['original_image'], true);
        }
        if (!empty($params['place'])) {
            $updateData['place_id'] = $this->getPlaceId($params['place']);
        } else {
            $updateData['place_id'] = null;
        }
        $this->repository->update($updateData, $post->id);
        $tagList = !empty($params['tags']) ? $params['tags'] : [];
        $this->postTagHandle($post, $tagList, TagHandleModeEnum::UPDATE_POST);
        $postDetail = $this->getDetail($params)->toArray();
        $postDetail['data']['post_id'] = $postDetail['data']['id'];
        return [
            'changeImage' => $changeImage,
            'oldImage' => $oldImage,
            'newImage' => !empty($updateData['image']) ? $updateData['image'] : null,
            'oldOriginalImage' => $oldOriginalImage,
            'newOriginalImage' => !empty($updateData['original_image']) ? $updateData['original_image'] : null,
            'post' => $postDetail
        ];
    }

    /**
     * Generate post image path, contain date, random string and post id
     * Example: posts/2021/12/23/KJLzNnWHHyDdyYyVPqGA8YKu8RbB1c-38.jpg
     * @param Post $post
     * @param UploadedFile $file
     * @param boolean $isOriginal
     * @return string
     */
    private function generatePostImagePath(Post $post, UploadedFile $file, bool $isOriginal = false): string
    {
        $basePath = config('settings.storageBasePath.postsImage');
        $date = Carbon::now()->format('Y/m/d');
        $hashString = Str::random(30);
        $fileExtension = $file->extension();
        if ($isOriginal) {
            $fileName = $this->generatePostOriginalImageName($post->image);
        } else {
            $fileName = "{$hashString}-{$post->id}";
        }
        return "{$basePath}/{$date}/{$fileName}.{$fileExtension}";
    }

    private function generatePostOriginalImageName($fullImageName): string
    {
        $baseName = basename($fullImageName);
        $imageNameNoExtension = preg_replace("/\.[^.]+$/", "", $baseName);
        return "{$imageNameNoExtension}_o";
    }

    /**
     * @param $post
     * @param $tagList
     * @param $mode
     */
    public function postTagHandle($post, $tagList, $mode)
    {
        //tag ton tai trong db
        $existsTag = $this->tagRepository->findWhereIn('name', $tagList)
            ->pluck('name', 'id')
            ->toArray();

        //tag moi
        $newTagList = array_diff($tagList, $existsTag);
        $newTagIds = [];
        if ($newTagList) {
            foreach ($newTagList as $newTagName) {
                $newTag = $this->tagRepository->create([
                    'name' => $newTagName,
                    'created_by' => Auth::id()
                ]);
                $newTagIds[] = $newTag->id;
            }
        }

        $tagIdList = array_merge(array_keys($existsTag), $newTagIds);
        $postTagData = [];
        $now = Carbon::now();
        foreach ($tagIdList as $tagId) {
            $postTagData[] = [
                'post_id' => $post->id,
                'tag_id' => $tagId,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        switch ($mode) {
            case TagHandleModeEnum::CREATE_POST:
                $this->postTagRepository->insert($postTagData);
                break;
            case TagHandleModeEnum::UPDATE_POST:
                $queryDelete = $this->postTagRepository->where([
                    'post_id' => $post->id
                ]);
                if (!empty($tagIdList)) {
                    $queryDelete->whereNotIn('tag_id', $tagIdList);
                }
                $queryDelete->delete();
                $this->postTagRepository->upsert($postTagData, ['post_id', 'tag_id'], ['updated_at']);
                break;
            default:
                break;
        }
    }

    /**
     * @param $params
     * @return Fractal
     * @throws NotFoundException
     */
    public function showPostForEdit($params): Fractal
    {
        $currentUser = Auth::user();
        if ($this->isBanned($currentUser)) {
            throw new NotFoundException(trans('exception.record_not_found'));
        }
        $postId = $params['postId'];
        $checkPost = $this->findWhere([
            'id' => $postId,
            'user_id' => $currentUser->id,
        ])->first();
        if (!$checkPost) {
            throw new NotFoundException(trans('exception.record_not_found'));
        }
        $detailPost = $this->repository->getPostForEdit($postId)
            ->with(['tags', 'place'])
            ->first();
        return fractal($detailPost, new PostShowTransformer());
    }

    /**
     * @param $params
     * @return array
     * @throws NotFoundException
     */
    public function deletePost($params): array
    {
        $post = $this->findWhere([
            'id' => $params['postId'],
            'user_id' => Auth::id(),
        ])->first();
        if ($post == null) {
            throw new NotFoundException(trans('messages.posts.not_found'));
        }
        $this->handleDeletePost($post);
        return [
            'message' => trans('messages.posts.delete_success')
        ];
    }

    /**
     * @param $post
     */
    public function handleDeletePost($post)
    {
        if ($post->status == PostStatusEnum::DRAFT) {
            $post->forceDelete();
            $this->postTagRepository->deleteWhere(['post_id' => $post->id]);
        } else {
            $this->commentRepository->deleteWhere(['post_id' => $post->id]);
        }
        $this->deleteWhere(['id' => $post->id]);
    }
}
