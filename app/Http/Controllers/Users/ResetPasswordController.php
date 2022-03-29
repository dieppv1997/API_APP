<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Http\Controllers\Users;

use App\Exceptions\BadRequestException;
use App\Exceptions\CheckAuthorizationException;
use App\Exceptions\NotFoundException;
use App\Exceptions\QueryException;
use App\Exceptions\ServerException;
use App\Exceptions\UnprocessableEntityException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Users\RequestResetPasswordRequest;
use App\Http\Requests\Users\ResetPasswordRequest;
use App\Interfaces\Services\Users\ResetPasswordServiceInterface;
use Illuminate\Http\JsonResponse;

class ResetPasswordController extends Controller
{
    protected $resetPasswordService;

    public function __construct(ResetPasswordServiceInterface $resetPasswordService)
    {
        $this->resetPasswordService = $resetPasswordService;
    }

    /**
     * @param RequestResetPasswordRequest $request
     * @return JsonResponse
     * @throws BadRequestException
     * @throws CheckAuthorizationException
     * @throws NotFoundException
     * @throws QueryException
     * @throws ServerException
     * @throws UnprocessableEntityException
     */
    public function requestResetPassword(RequestResetPasswordRequest $request): JsonResponse
    {
        $params = $request->all();
        return $this->doRequest(function () use ($params) {
            return $this->resetPasswordService->requestResetPassword($params);
        }, $request);
    }

    /**
     * @param ResetPasswordRequest $request
     * @return JsonResponse
     * @throws BadRequestException
     * @throws CheckAuthorizationException
     * @throws NotFoundException
     * @throws QueryException
     * @throws ServerException
     * @throws UnprocessableEntityException
     */
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $params = $request->all();
        return $this->doRequest(function () use ($params) {
            return $this->resetPasswordService->resetPassword($params);
        }, $request);
    }
}
