<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Exceptions\BadRequestException;
use App\Exceptions\CheckAuthorizationException;
use App\Exceptions\NotFoundException;
use App\Exceptions\QueryException;
use App\Exceptions\ServerException;
use App\Exceptions\UnprocessableEntityException;
use App\Http\Requests\Authentication\LoginRequest;
use App\Http\Requests\Authentication\LogoutRequest;
use App\Http\Requests\Authentication\RegisterEmailNicknameRequest;
use App\Http\Requests\Authentication\RegisterEmailRequest;
use App\Http\Requests\Authentication\RegisterByNicknameRequest;
use App\Http\Requests\Authentication\VerifyEmailNicknameRequest;
use App\Http\Requests\Authentication\VerifyEmailRequest;
use App\Interfaces\Services\Users\AuthenticationServiceInterface;
use App\Services\Users\AuthenticationService;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    /**
     * @var AuthenticationService
     */
    protected $authenticationService;

    public function __construct(AuthenticationServiceInterface $authenticationService)
    {
        $this->authenticationService = $authenticationService;
    }

    /**
     * @param RegisterEmailNicknameRequest $request
     * @return JsonResponse
     * @throws BadRequestException
     * @throws CheckAuthorizationException
     * @throws NotFoundException
     * @throws QueryException
     * @throws ServerException
     * @throws UnprocessableEntityException
     */
    public function registerEmailNickname(RegisterEmailNicknameRequest $request): JsonResponse
    {
        $params = $request->all();
        return $this->doRequest(function () use ($params) {
            return $this->authenticationService->registerEmailNickname($params);
        }, $request);
    }

    /**
     * @param RegisterEmailRequest $request
     * @return JsonResponse
     * @throws BadRequestException
     * @throws CheckAuthorizationException
     * @throws NotFoundException
     * @throws QueryException
     * @throws ServerException
     * @throws UnprocessableEntityException
     */
    public function registerEmail(RegisterEmailRequest $request): JsonResponse
    {
        $params = $request->all();
        return $this->doRequest(function () use ($params) {
            return $this->authenticationService->registerEmail($params);
        }, $request);
    }

    /**
     * @param RegisterByNicknameRequest $request
     * @return JsonResponse
     * @throws BadRequestException
     * @throws CheckAuthorizationException
     * @throws NotFoundException
     * @throws QueryException
     * @throws ServerException
     * @throws UnprocessableEntityException
     */
    public function registerByNickname(RegisterByNicknameRequest $request): JsonResponse
    {
        $params = $request->all();
        return $this->doRequest(function () use ($params) {
            return $this->authenticationService->registerByNickname($params);
        }, $request);
    }

    /**
     * @param LoginRequest $request
     * @return JsonResponse
     * @throws BadRequestException
     * @throws CheckAuthorizationException
     * @throws NotFoundException
     * @throws QueryException
     * @throws ServerException
     * @throws UnprocessableEntityException
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $params = $request->all();
        return $this->doRequest(function () use ($params) {
            return $this->authenticationService->login($params);
        }, $request);
    }

    /**
     * @param LogoutRequest $request
     * @return JsonResponse
     * @throws BadRequestException
     * @throws CheckAuthorizationException
     * @throws NotFoundException
     * @throws QueryException
     * @throws ServerException
     * @throws UnprocessableEntityException
     */
    public function logout(LogoutRequest $request): JsonResponse
    {
        $params = $request->all();
        return $this->doRequest(function () use ($params) {
            return $this->authenticationService->logout($params);
        }, $request);
    }

    /**
     * @param VerifyEmailRequest $request
     * @return JsonResponse
     * @throws BadRequestException
     * @throws CheckAuthorizationException
     * @throws NotFoundException
     * @throws QueryException
     * @throws ServerException
     * @throws UnprocessableEntityException
     */
    public function verifyEmail(VerifyEmailRequest $request): JsonResponse
    {
        $params = $request->all();
        return $this->doRequest(function () use ($params) {
            return $this->authenticationService->verifyEmail($params);
        }, $request);
    }

    /**
     * @param VerifyEmailNicknameRequest $request
     * @return JsonResponse
     * @throws BadRequestException
     * @throws CheckAuthorizationException
     * @throws NotFoundException
     * @throws QueryException
     * @throws ServerException
     * @throws UnprocessableEntityException
     */
    public function verifyEmailNickname(VerifyEmailNicknameRequest $request): JsonResponse
    {
        $params = $request->all();
        return $this->doRequest(function () use ($params) {
            return $this->authenticationService->verifyEmailNickname($params);
        }, $request);
    }
}
