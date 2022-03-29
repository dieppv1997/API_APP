<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\Following\ApproveFollowRequest;
use App\Http\Requests\Following\FollowUserRequest;
use App\Http\Requests\Following\RejectFollowRequest;
use App\Http\Requests\Following\UnFollowUserRequest;
use App\Interfaces\Services\Following\FollowingServiceInterface;
use App\Exceptions\BadRequestException;
use App\Exceptions\CheckAuthorizationException;
use App\Exceptions\NotFoundException;
use App\Exceptions\QueryException;
use App\Exceptions\ServerException;
use App\Exceptions\UnprocessableEntityException;

use Illuminate\Http\JsonResponse;

class FollowingController extends Controller
{
    public $followingService;

    /**
     * FollowingController constructor.
     * @param FollowingServiceInterface $followingService
     */
    public function __construct(FollowingServiceInterface $followingService)
    {
        $this->followingService = $followingService;
    }

    /**
     * @param FollowUserRequest $request
     * @return JsonResponse
     * @throws BadRequestException
     * @throws CheckAuthorizationException
     * @throws NotFoundException
     * @throws QueryException
     * @throws ServerException
     * @throws UnprocessableEntityException
     */
    public function followUser(FollowUserRequest $request): JsonResponse
    {
        $params = $request->all();
        return $this->doRequest(function () use ($params) {
            return $this->followingService->followUser($params);
        }, $request);
    }

    /**
     * @param UnFollowUserRequest $request
     * @return JsonResponse
     * @throws BadRequestException
     * @throws CheckAuthorizationException
     * @throws NotFoundException
     * @throws QueryException
     * @throws ServerException
     * @throws UnprocessableEntityException
     */
    public function unFollowUser(UnFollowUserRequest $request): JsonResponse
    {
        $params = $request->all();
        return $this->doRequest(function () use ($params) {
            return $this->followingService->unFollowUser($params);
        }, $request);
    }

    /**
     * @param ApproveFollowRequest $request
     * @return JsonResponse
     * @throws BadRequestException
     * @throws CheckAuthorizationException
     * @throws NotFoundException
     * @throws QueryException
     * @throws ServerException
     * @throws UnprocessableEntityException
     */
    public function approveFollow(ApproveFollowRequest $request): JsonResponse
    {
        $params = $request->all();
        return $this->doRequest(function () use ($params) {
            return $this->followingService->approveFollow($params);
        }, $request);
    }

    /**
     * @param RejectFollowRequest $request
     * @return JsonResponse
     * @throws BadRequestException
     * @throws CheckAuthorizationException
     * @throws NotFoundException
     * @throws QueryException
     * @throws ServerException
     * @throws UnprocessableEntityException
     */
    public function rejectFollow(RejectFollowRequest $request): JsonResponse
    {
        $params = $request->all();
        return $this->doRequest(function () use ($params) {
            return $this->followingService->rejectFollow($params);
        }, $request);
    }
}
