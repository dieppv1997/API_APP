<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Http\Controllers\Users;

use App\Exceptions\CheckAuthenticationException;
use App\Http\Controllers\Controller;
use App\Exceptions\BadRequestException;
use App\Exceptions\CheckAuthorizationException;
use App\Exceptions\NotFoundException;
use App\Exceptions\QueryException;
use App\Exceptions\ServerException;
use App\Exceptions\UnprocessableEntityException;
use App\Http\Requests\Users\ListFollowerUserRequest;
use App\Http\Requests\Users\ProfileRequest;
use App\Http\Requests\Recommendation\ListRecommendUserRequest;
use App\Http\Requests\Users\ChangePasswordRequest;
use App\Http\Requests\Users\UpdateProfileRequest;
use App\Interfaces\Services\Images\ImageHandleServiceInterface;
use App\Interfaces\Services\Users\UserPasswordServiceInterface;
use App\Interfaces\Services\Users\UserServiceInterface;
use App\Services\Recommendations\RecommendUserService;
use App\Services\Users\UserService;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * @var UserService
     */
    protected $userService;
    protected $userPasswordService;
    protected $recommendUserService;
    protected $imageHandleService;

    /**
     * UserController constructor.
     * @param UserServiceInterface $userService
     * @param UserPasswordServiceInterface $userPasswordService
     * @param RecommendUserService $recommendUserService
     * @param ImageHandleServiceInterface $imageHandleService
     */
    public function __construct(
        UserServiceInterface $userService,
        UserPasswordServiceInterface $userPasswordService,
        RecommendUserService $recommendUserService,
        ImageHandleServiceInterface $imageHandleService
    ) {
        $this->userService = $userService;
        $this->userPasswordService = $userPasswordService;
        $this->recommendUserService = $recommendUserService;
        $this->imageHandleService = $imageHandleService;
    }

    /**
     * @param ChangePasswordRequest $request
     * @return JsonResponse
     * @throws BadRequestException
     * @throws CheckAuthorizationException
     * @throws NotFoundException
     * @throws QueryException
     * @throws ServerException
     * @throws UnprocessableEntityException
     */
    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        $params = $request->all();
        return $this->doRequest(function () use ($params) {
            return $this->userPasswordService->changePassword($params);
        }, $request);
    }

    /**
     * @param ListRecommendUserRequest $request
     * @return JsonResponse
     * @throws BadRequestException
     * @throws CheckAuthorizationException
     * @throws NotFoundException
     * @throws QueryException
     * @throws ServerException
     * @throws UnprocessableEntityException
     */
    public function getListRecommendUser(ListRecommendUserRequest $request): JsonResponse
    {
        $params = $request->all();
        return $this->doRequest(function () use ($params) {
            return $this->recommendUserService->getListRecommendUser($params);
        }, $request);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws BadRequestException
     * @throws CheckAuthorizationException
     * @throws NotFoundException
     * @throws QueryException
     * @throws ServerException
     * @throws UnprocessableEntityException
     */
    public function user(Request $request): JsonResponse
    {
        $user = $request->user();
        return $this->doRequest(function () use ($user) {
            return $this->userService->getDetail($user);
        }, $request);
    }

    /**
     * @param ProfileRequest $request
     * @return JsonResponse
     * @throws CheckAuthorizationException
     * @throws NotFoundException
     * @throws QueryException
     * @throws ServerException
     * @throws UnprocessableEntityException
     * @throws CheckAuthenticationException
     */
    public function profile(ProfileRequest $request): JsonResponse
    {
        $params = $request->all();
        return $this->getData(function () use ($params) {
            return $this->userService->profile($params);
        }, $request);
    }

    /**
     * @param UpdateProfileRequest $request
     * @return JsonResponse
     * @throws BadRequestException
     * @throws CheckAuthorizationException
     * @throws NotFoundException
     * @throws QueryException
     * @throws ServerException
     * @throws UnprocessableEntityException
     */
    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        $params = $request->all();
        return $this->doRequest(function () use ($params) {
            $updateProfile = $this->userService->updateProfile($params);
            if ($updateProfile['changeAvatar']) {
                $this->imageHandleService->uploadImage($params['avatar_image'], $updateProfile['newAvatar']);
                $this->imageHandleService->deleteImage($updateProfile['oldAvatar']);
            }
            if ($updateProfile['changeCover']) {
                $this->imageHandleService->uploadImage($params['cover_image'], $updateProfile['newCover']);
                $this->imageHandleService->deleteImage($updateProfile['oldCover']);
            }
            return [
                'message' => trans('messages.user.edit_success'),
            ];
        }, $request);
    }

    /**
     * @param ListFollowerUserRequest $request
     * @return JsonResponse
     * @throws CheckAuthenticationException
     * @throws CheckAuthorizationException
     * @throws NotFoundException
     * @throws QueryException
     * @throws ServerException
     * @throws UnprocessableEntityException
     */
    public function getListFollowers(ListFollowerUserRequest $request): JsonResponse
    {
        $params = $request->all();
        return $this->getData(function () use ($params) {
            return $this->userService->getListFollowers($params);
        }, $request);
    }

    /**
     * @param ListFollowerUserRequest $request
     * @return JsonResponse
     * @throws CheckAuthenticationException
     * @throws CheckAuthorizationException
     * @throws NotFoundException
     * @throws QueryException
     * @throws ServerException
     * @throws UnprocessableEntityException
     */
    public function getListFollowing(ListFollowerUserRequest $request): JsonResponse
    {
        $params = $request->all();
        return $this->getData(function () use ($params) {
            return $this->userService->getListFollowing($params);
        }, $request);
    }
}
