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
use App\Http\Requests\Users\RequestChangeEmailRequest;
use App\Http\Requests\Users\VerifyChangeEmailRequest;
use App\Services\Users\ChangeEmailService;

use Illuminate\Http\JsonResponse;

class ChangeEmailController extends Controller
{
    /**
     * @var ChangeEmailService
     */
    protected $changeEmailService;

    public function __construct(ChangeEmailService $changeEmailService)
    {
        $this->changeEmailService = $changeEmailService;
    }

    /**
     * @param RequestChangeEmailRequest $request
     * @return JsonResponse
     * @throws BadRequestException
     * @throws CheckAuthorizationException
     * @throws NotFoundException
     * @throws QueryException
     * @throws ServerException
     * @throws UnprocessableEntityException
     */
    public function requestChangeEmail(RequestChangeEmailRequest $request): JsonResponse
    {
        $params = $request->all();
        return $this->doRequest(function () use ($params) {
            return $this->changeEmailService->requestChangeEmail($params);
        }, $request);
    }

    /**
     * @param VerifyChangeEmailRequest $request
     * @return JsonResponse
     * @throws BadRequestException
     * @throws CheckAuthorizationException
     * @throws NotFoundException
     * @throws QueryException
     * @throws ServerException
     * @throws UnprocessableEntityException
     */
    public function verifyChangeEmail(VerifyChangeEmailRequest $request): JsonResponse
    {
        $params = $request->all();
        return $this->doRequest(function () use ($params) {
            return $this->changeEmailService->verifyChangeEmail($params);
        }, $request);
    }
}
