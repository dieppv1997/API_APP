<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Http\Controllers\Posts;

use App\Enums\Posts\PostStatusEnum;
use App\Enums\Posts\PostUpdateStatusEnum;
use App\Exceptions\BadRequestException;
use App\Exceptions\CheckAuthenticationException;
use App\Exceptions\CheckAuthorizationException;
use App\Exceptions\NotFoundException;
use App\Exceptions\QueryException;
use App\Exceptions\ServerException;
use App\Exceptions\UnprocessableEntityException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Posts\CreatePostRequest;
use App\Http\Requests\Posts\DeletePostRequest;
use App\Http\Requests\Posts\DetailPostRequest;
use App\Http\Requests\Posts\LikePostRequest;
use App\Http\Requests\Posts\ShowPostForEditRequest;
use App\Http\Requests\Posts\UnlikePostRequest;
use App\Http\Requests\Posts\UpdatePostRequest;
use App\Interfaces\Services\Images\ImageHandleServiceInterface;
use App\Interfaces\Services\Posts\PostServiceInterface;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class PostController extends Controller
{
    /**
     * @var PostServiceInterface
     */
    protected $postService;
    protected $imageHandleService;

    public function __construct(
        PostServiceInterface $postService,
        ImageHandleServiceInterface $imageHandleService
    ) {
        $this->postService = $postService;
        $this->imageHandleService = $imageHandleService;
    }

    /**
     * @param DetailPostRequest $request
     * @return JsonResponse
     * @throws CheckAuthenticationException
     * @throws CheckAuthorizationException
     * @throws NotFoundException
     * @throws QueryException
     * @throws ServerException
     * @throws UnprocessableEntityException
     */
    public function getDetail(DetailPostRequest $request): JsonResponse
    {
        $params = $request->all();
        return $this->getData(function () use ($params) {
            return $this->postService->getDetail($params);
        }, $request);
    }

    /**
     * @param LikePostRequest $request
     * @return JsonResponse
     * @throws CheckAuthorizationException
     * @throws NotFoundException
     * @throws QueryException
     * @throws ServerException
     * @throws UnprocessableEntityException
     * @throws BadRequestException
     */
    public function likePost(LikePostRequest $request): JsonResponse
    {
        $params = $request->all();
        return $this->doRequest(function () use ($params) {
            return $this->postService->likePost($params);
        }, $request);
    }

    /**
     * @param UnlikePostRequest $request
     * @return JsonResponse
     * @throws BadRequestException
     * @throws CheckAuthorizationException
     * @throws NotFoundException
     * @throws QueryException
     * @throws ServerException
     * @throws UnprocessableEntityException
     */
    public function unlikePost(UnlikePostRequest $request): JsonResponse
    {
        $params = $request->all();
        return $this->doRequest(function () use ($params) {
            return $this->postService->unlikePost($params);
        }, $request);
    }

    /**
     * @param CreatePostRequest $request
     * @return JsonResponse
     * @throws BadRequestException
     * @throws CheckAuthorizationException
     * @throws NotFoundException
     * @throws QueryException
     * @throws ServerException
     * @throws UnprocessableEntityException
     */
    public function createPost(CreatePostRequest $request): JsonResponse
    {
        $params = $request->all();
        $file = $request->file('image');
        return $this->doRequest(function () use ($params, $file) {
            $post = $this->postService->createPost($params);
            $uploadPostImage = $this->imageHandleService->uploadImage($params['image'], $post['image']);
            $uploadPostOriginalImage = $this->imageHandleService->uploadImage(
                $params['original_image'],
                $post['original_image']
            );
            if (!$uploadPostImage || !$uploadPostOriginalImage) {
                throw new ServerException();
            }
            return [
                'message' => $params['type'] == PostStatusEnum::PUBLISHED
                    ? trans('messages.posts.create_success')
                    : trans('messages.draft.create_success'),
                'data' => [
                    'post_id' => $post['id']
                ]
            ];
        }, $request, Response::HTTP_CREATED);
    }

    /**
     * @param UpdatePostRequest $request
     * @return JsonResponse
     * @throws BadRequestException
     * @throws CheckAuthorizationException
     * @throws NotFoundException
     * @throws QueryException
     * @throws ServerException
     * @throws UnprocessableEntityException
     */
    public function updatePost(UpdatePostRequest $request): JsonResponse
    {
        $params = $request->all();
        $mode = PostUpdateStatusEnum::UPDATE;
        return $this->doRequest(function () use ($params, $mode) {
            $updatePost = $this->postService->updatePost($params, $mode);
            if ($updatePost['changeImage']) {
                $this->imageHandleService->uploadImage($params['image'], $updatePost['newImage']);
                $this->imageHandleService->deleteImage($updatePost['oldImage']);
                $this->imageHandleService->uploadImage($params['original_image'], $updatePost['newOriginalImage']);
                $this->imageHandleService->deleteImage($updatePost['oldOriginalImage']);
            }
            return [
                'message' => trans('messages.posts.edit_success'),
                'data' => $updatePost['post']['data']
            ];
        }, $request);
    }

    public function showPostForEdit(ShowPostForEditRequest $request): JsonResponse
    {
        $params = $request->all();
        return $this->getData(function () use ($params) {
            return $this->postService->showPostForEdit($params);
        }, $request);
    }

    /**
     * @param UpdatePostRequest $request
     * @return JsonResponse
     * @throws BadRequestException
     * @throws CheckAuthorizationException
     * @throws NotFoundException
     * @throws QueryException
     * @throws ServerException
     * @throws UnprocessableEntityException
     */
    public function publicPost(UpdatePostRequest $request): JsonResponse
    {
        $params = $request->all();
        $mode = PostUpdateStatusEnum::PUBLISHED;
        return $this->doRequest(function () use ($params, $mode) {
            $updatePost = $this->postService->updatePost($params, $mode);
            if ($updatePost['changeImage']) {
                $this->imageHandleService->uploadImage($params['image'], $updatePost['newImage']);
                $this->imageHandleService->deleteImage($updatePost['oldImage']);
            }
            return [
                'message' => trans('messages.posts.create_success'),
                'data' => $updatePost['post']['data']
            ];
        }, $request);
    }

    /**
     * @param DeletePostRequest $request
     * @return JsonResponse
     * @throws BadRequestException
     * @throws CheckAuthorizationException
     * @throws NotFoundException
     * @throws QueryException
     * @throws ServerException
     * @throws UnprocessableEntityException
     */
    public function deletePost(DeletePostRequest $request): JsonResponse
    {
        $params = $request->all();
        return $this->doRequest(function () use ($params) {
            return $this->postService->deletePost($params);
        }, $request);
    }
}
