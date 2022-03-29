<?php

namespace App\Http\Controllers\App;

use App\Exceptions\BadRequestException;
use App\Exceptions\CheckAuthorizationException;
use App\Exceptions\NotFoundException;
use App\Exceptions\QueryException;
use App\Exceptions\ServerException;
use App\Exceptions\UnprocessableEntityException;
use App\Http\Controllers\Controller;
use App\Http\Requests\App\StorageFcmTokenRequest;
use App\Interfaces\Services\App\AppSettingServiceInterface;
use Illuminate\Http\JsonResponse;

class AppSettingController extends Controller
{
    protected $appSettingService;

    public function __construct(AppSettingServiceInterface $appSettingService)
    {
        $this->appSettingService = $appSettingService;
    }

    /**
     * @param StorageFcmTokenRequest $request
     * @return JsonResponse
     * @throws BadRequestException
     * @throws CheckAuthorizationException
     * @throws NotFoundException
     * @throws QueryException
     * @throws ServerException
     * @throws UnprocessableEntityException
     */
    public function storageFcmToken(StorageFcmTokenRequest $request): JsonResponse
    {
        $params = $request->all();
        return $this->doRequest(function () use ($params) {
            return $this->appSettingService->storageFcmToken($params);
        }, $request);
    }
}
