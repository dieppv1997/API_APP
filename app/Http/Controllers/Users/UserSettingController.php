<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Http\Controllers\Users;

use App\Exceptions\BadRequestException;
use App\Exceptions\CheckAuthenticationException;
use App\Exceptions\CheckAuthorizationException;
use App\Exceptions\NotFoundException;
use App\Exceptions\QueryException;
use App\Exceptions\ServerException;
use App\Exceptions\UnprocessableEntityException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Users\SettingNotificationRequest;
use App\Http\Requests\Users\SettingPrivacyRequest;
use App\Interfaces\Services\Users\UserSettingServiceInterface;

use App\Services\Users\UserSettingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserSettingController extends Controller
{
    /**
     * @var UserSettingService
     */
    protected $userSettingService;

    /**
     * UserSettingController constructor.
     * @param UserSettingServiceInterface $userSettingService
     */
    public function __construct(UserSettingServiceInterface $userSettingService)
    {
        $this->userSettingService = $userSettingService;
    }

    /**
     * @param SettingNotificationRequest $request
     * @return JsonResponse
     * @throws BadRequestException
     * @throws CheckAuthorizationException
     * @throws NotFoundException
     * @throws QueryException
     * @throws ServerException
     * @throws UnprocessableEntityException
     */
    public function settingNotification(SettingNotificationRequest $request): JsonResponse
    {
        $params = $request->all();
        return $this->doRequest(function () use ($params) {
            return $this->userSettingService->settingNotification($params);
        }, $request);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws CheckAuthenticationException
     * @throws CheckAuthorizationException
     * @throws NotFoundException
     * @throws QueryException
     * @throws ServerException
     * @throws UnprocessableEntityException
     */
    public function getSettingNotification(Request $request): JsonResponse
    {
        $params = $request->all();
        return $this->getData(function () use ($params) {
            return $this->userSettingService->getSettingNotification();
        }, $request);
    }

    /**
     * @param SettingPrivacyRequest $request
     * @return JsonResponse
     * @throws CheckAuthorizationException
     * @throws NotFoundException
     * @throws QueryException
     * @throws ServerException
     * @throws UnprocessableEntityException
     * @throws BadRequestException
     */
    public function settingPrivacy(SettingPrivacyRequest $request): JsonResponse
    {
        $params = $request->all();
        return $this->doRequest(function () use ($params) {
            return $this->userSettingService->settingPrivacy($params);
        }, $request);
    }
}
