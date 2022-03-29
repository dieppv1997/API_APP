<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Http\Controllers\Comments;

use App\Exceptions\BadRequestException;
use App\Exceptions\CheckAuthenticationException;
use App\Exceptions\CheckAuthorizationException;
use App\Exceptions\NotFoundException;
use App\Exceptions\QueryException;
use App\Exceptions\ServerException;
use App\Exceptions\UnprocessableEntityException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Comments\DeleteCommentRequest;
use App\Http\Requests\Comments\LikeCommentRequest;
use App\Http\Requests\Comments\ListCommentByPostRequest;
use App\Http\Requests\Comments\PostCommentRequest;
use App\Http\Requests\Comments\UnlikeCommentRequest;
use App\Http\Requests\Comments\UpdateCommentRequest;
use App\Interfaces\Services\Comments\CommentServiceInterface;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class CommentController extends Controller
{
    public $commentService;

    /**
     * CommentController constructor.
     * @param CommentServiceInterface $commentService
     */
    public function __construct(CommentServiceInterface $commentService)
    {
        $this->commentService = $commentService;
    }

    /**
     * @param ListCommentByPostRequest $request
     * @return JsonResponse
     * @throws CheckAuthenticationException
     * @throws CheckAuthorizationException
     * @throws NotFoundException
     * @throws QueryException
     * @throws ServerException
     * @throws UnprocessableEntityException
     */
    public function listCommentByPost(ListCommentByPostRequest $request): JsonResponse
    {
        $params = $request->all();
        return $this->getData(function () use ($params) {
            return $this->commentService->listCommentByPost($params);
        }, $request);
    }

    /**
     * @param PostCommentRequest $request
     * @return JsonResponse
     * @throws CheckAuthorizationException
     * @throws NotFoundException
     * @throws QueryException
     * @throws ServerException
     * @throws UnprocessableEntityException
     * @throws BadRequestException
     */
    public function postComment(PostCommentRequest $request): JsonResponse
    {
        $params = $request->all();
        return $this->doRequest(function () use ($params) {
            return $this->commentService->postComment($params);
        }, $request, Response::HTTP_CREATED);
    }

    /**
     * @param UpdateCommentRequest $request
     * @return JsonResponse
     * @throws BadRequestException
     * @throws CheckAuthorizationException
     * @throws NotFoundException
     * @throws QueryException
     * @throws ServerException
     * @throws UnprocessableEntityException
     */
    public function updateComment(UpdateCommentRequest $request): JsonResponse
    {
        $params = $request->all();
        return $this->doRequest(function () use ($params) {
            return $this->commentService->updateComment($params);
        }, $request);
    }

    /**
     * @param DeleteCommentRequest $request
     * @return JsonResponse
     * @throws BadRequestException
     * @throws CheckAuthorizationException
     * @throws NotFoundException
     * @throws QueryException
     * @throws ServerException
     * @throws UnprocessableEntityException
     */
    public function deleteComment(DeleteCommentRequest $request): JsonResponse
    {
        $params = $request->all();
        return $this->doRequest(function () use ($params) {
            return $this->commentService->deleteComment($params);
        }, $request);
    }

    /**
     * @param LikeCommentRequest $request
     * @return JsonResponse
     * @throws BadRequestException
     * @throws CheckAuthorizationException
     * @throws NotFoundException
     * @throws QueryException
     * @throws ServerException
     * @throws UnprocessableEntityException
     */
    public function likeComment(LikeCommentRequest $request): JsonResponse
    {
        $params = $request->all();
        return $this->doRequest(function () use ($params) {
            return $this->commentService->likeComment($params);
        }, $request);
    }

    /**
     * @param UnlikeCommentRequest $request
     * @return JsonResponse
     * @throws BadRequestException
     * @throws CheckAuthorizationException
     * @throws NotFoundException
     * @throws QueryException
     * @throws ServerException
     * @throws UnprocessableEntityException
     */
    public function unlikeComment(UnlikeCommentRequest $request): JsonResponse
    {
        $params = $request->all();
        return $this->doRequest(function () use ($params) {
            return $this->commentService->unlikeComment($params);
        }, $request);
    }
}
