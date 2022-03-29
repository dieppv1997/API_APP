<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Http\Controllers\Notifications;

use App\Exceptions\BadRequestException;
use App\Exceptions\CheckAuthenticationException;
use App\Exceptions\CheckAuthorizationException;
use App\Exceptions\NotFoundException;
use App\Exceptions\QueryException;
use App\Exceptions\ServerException;
use App\Exceptions\UnprocessableEntityException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Notifications\ListNotificationRequest;
use App\Http\Requests\Notifications\MakeAsReadByTypeRequest;
use App\Http\Requests\Notifications\MakeAsReadRequest;
use App\Interfaces\Services\Notifications\NotificationServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationServiceInterface $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * @param ListNotificationRequest $request
     * @return JsonResponse
     * @throws BadRequestException
     * @throws CheckAuthorizationException
     * @throws NotFoundException
     * @throws QueryException
     * @throws ServerException
     * @throws UnprocessableEntityException
     */
    public function getListByUser(ListNotificationRequest $request): JsonResponse
    {
        $params = $request->all();
        return $this->doRequest(function () use ($params) {
            return $this->notificationService->getListByUser($params);
        }, $request);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws CheckAuthorizationException
     * @throws NotFoundException
     * @throws QueryException
     * @throws ServerException
     * @throws UnprocessableEntityException
     * @throws CheckAuthenticationException
     */
    public function getStatus(Request $request): JsonResponse
    {
        return $this->getData(function () {
            return $this->notificationService->getStatus();
        }, $request);
    }

    /**
     * @param MakeAsReadRequest $request
     * @return JsonResponse
     * @throws BadRequestException
     * @throws CheckAuthorizationException
     * @throws NotFoundException
     * @throws QueryException
     * @throws ServerException
     * @throws UnprocessableEntityException
     */
    public function makeOfficialAsRead(MakeAsReadRequest $request): JsonResponse
    {
        $params = $request->all();
        return $this->doRequest(function () use ($params) {
            return $this->notificationService->makeOfficialAsRead($params);
        }, $request);
    }

    /**
     * @param MakeAsReadByTypeRequest $request
     * @return JsonResponse
     * @throws BadRequestException
     * @throws CheckAuthorizationException
     * @throws NotFoundException
     * @throws QueryException
     * @throws ServerException
     * @throws UnprocessableEntityException
     */
    public function markAsReadByType(MakeAsReadByTypeRequest $request): JsonResponse
    {
        $params = $request->all();
        return $this->doRequest(function () use ($params) {
            return $this->notificationService->markAsReadByType($params);
        }, $request);
    }
}
