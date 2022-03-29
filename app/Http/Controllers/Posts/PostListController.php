<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Http\Controllers\Posts;

use App\Exceptions\CheckAuthenticationException;
use App\Exceptions\CheckAuthorizationException;
use App\Exceptions\NotFoundException;
use App\Exceptions\QueryException;
use App\Exceptions\ServerException;
use App\Exceptions\UnprocessableEntityException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Posts\ListByFollowingUserRequest;
use App\Http\Requests\Posts\ListByTagAndIdRequest;
use App\Http\Requests\Posts\ListByTagRequest;
use App\Http\Requests\Posts\ListDraftByUserRequest;
use App\Http\Requests\Posts\ListMyPostRequest;
use App\Http\Requests\Posts\ListMyPostFromPostId;
use App\Http\Requests\Posts\ListNewestFromIdRequest;
use App\Http\Requests\Posts\ListNewestRequest;
use App\Http\Requests\Posts\ListPostLikedFromPostIdRequest;
use App\Http\Requests\Posts\ListPostLikedRequest;
use App\Interfaces\Services\Posts\PostListServiceInterface;
use Illuminate\Http\JsonResponse;

class PostListController extends Controller
{
    /**
     * @var PostListServiceInterface
     */
    protected $postListService;

    public function __construct(PostListServiceInterface $postListService)
    {
        $this->postListService = $postListService;
    }

    /**
     * @param ListByTagRequest $request
     * @return JsonResponse
     * @throws CheckAuthenticationException
     * @throws CheckAuthorizationException
     * @throws NotFoundException
     * @throws QueryException
     * @throws ServerException
     * @throws UnprocessableEntityException
     */
    public function listByTag(ListByTagRequest $request): JsonResponse
    {
        $params = $request->all();
        return $this->getData(function () use ($params) {
            return $this->postListService->listByTag($params);
        }, $request);
    }

    /**
     * @param ListByTagAndIdRequest $request
     * @return JsonResponse
     * @throws CheckAuthenticationException
     * @throws CheckAuthorizationException
     * @throws NotFoundException
     * @throws QueryException
     * @throws ServerException
     * @throws UnprocessableEntityException
     */
    public function listByTagAndId(ListByTagAndIdRequest $request): JsonResponse
    {
        $params = $request->all();
        return $this->getData(function () use ($params) {
            return $this->postListService->listByTagFromId($params);
        }, $request);
    }

    /**
     * @param ListNewestRequest $request
     * @return JsonResponse
     * @throws CheckAuthenticationException
     * @throws CheckAuthorizationException
     * @throws NotFoundException
     * @throws QueryException
     * @throws ServerException
     * @throws UnprocessableEntityException
     */
    public function listNewest(ListNewestRequest $request): JsonResponse
    {
        $params = $request->all();
        return $this->getData(function () use ($params) {
            return $this->postListService->getListNewest($params);
        }, $request);
    }

    /**
     * @param ListNewestFromIdRequest $request
     * @return JsonResponse
     * @throws CheckAuthenticationException
     * @throws CheckAuthorizationException
     * @throws NotFoundException
     * @throws QueryException
     * @throws ServerException
     * @throws UnprocessableEntityException
     */
    public function listNewestFromId(ListNewestFromIdRequest $request): JsonResponse
    {
        $params = $request->all();
        return $this->getData(function () use ($params) {
            return $this->postListService->listNewestFromId($params);
        }, $request);
    }

    /**
     * @param ListByFollowingUserRequest $request
     * @return JsonResponse
     * @throws CheckAuthenticationException
     * @throws CheckAuthorizationException
     * @throws NotFoundException
     * @throws QueryException
     * @throws ServerException
     * @throws UnprocessableEntityException
     */
    public function listByFollowingUser(ListByFollowingUserRequest $request): JsonResponse
    {
        $params = $request->all();
        return $this->getData(function () use ($params) {
            return $this->postListService->listByFollowingUser($params);
        }, $request);
    }

    /**
     * @param ListDraftByUserRequest $request
     * @return JsonResponse
     * @throws CheckAuthenticationException
     * @throws CheckAuthorizationException
     * @throws NotFoundException
     * @throws QueryException
     * @throws ServerException
     * @throws UnprocessableEntityException
     */
    public function listDraftByUser(ListDraftByUserRequest $request): JsonResponse
    {
        $params = $request->all();
        return $this->getData(function () use ($params) {
            return $this->postListService->listDraftByUser($params);
        }, $request);
    }

    public function listMyPost(ListMyPostRequest $request): JsonResponse
    {
        $params = $request->all();
        return $this->getData(function () use ($params) {
            return $this->postListService->listMyPostOfUser($params);
        }, $request);
    }

    public function listMyPostFromPostId(ListMyPostFromPostId $request): JsonResponse
    {
        $params = $request->all();
        return $this->getData(function () use ($params) {
            return $this->postListService->listMyPostFromId($params);
        }, $request);
    }

    /**
     * @param ListPostLikedRequest $request
     * @return JsonResponse
     * @throws CheckAuthenticationException
     * @throws CheckAuthorizationException
     * @throws NotFoundException
     * @throws QueryException
     * @throws ServerException
     * @throws UnprocessableEntityException
     */
    public function listPostLiked(ListPostLikedRequest $request): JsonResponse
    {
        $params = $request->all();
        return $this->getData(function () use ($params) {
            return $this->postListService->listPostLiked($params);
        }, $request);
    }

    /**
     * @param ListPostLikedFromPostIdRequest $request
     * @return JsonResponse
     * @throws CheckAuthenticationException
     * @throws CheckAuthorizationException
     * @throws NotFoundException
     * @throws QueryException
     * @throws ServerException
     * @throws UnprocessableEntityException
     */
    public function listPostLikedFromPostId(ListPostLikedFromPostIdRequest $request): JsonResponse
    {
        $params = $request->all();
        return $this->getData(function () use ($params) {
            return $this->postListService->listPostLikedFromPostId($params);
        }, $request);
    }
}
