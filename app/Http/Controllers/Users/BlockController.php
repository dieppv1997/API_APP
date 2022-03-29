<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\Block\BlockUserRequest;
use App\Http\Requests\Block\UnBlockUserRequest;
use App\Http\Requests\Block\ListBlockUserRequest;
use App\Interfaces\Services\Block\BlockServiceInterface;
use App\Exceptions\BadRequestException;
use App\Exceptions\CheckAuthorizationException;
use App\Exceptions\NotFoundException;
use App\Exceptions\QueryException;
use App\Exceptions\ServerException;
use App\Exceptions\UnprocessableEntityException;

use Illuminate\Http\JsonResponse;

class BlockController extends Controller
{
    public $blockService;

    /**
     * BlockController constructor.
     * @param BlockServiceInterface $blockService
     */
    public function __construct(BlockServiceInterface $blockService)
    {
        $this->blockService = $blockService;
    }

    /**
     * @param BlockUserRequest $request
     * @return JsonResponse
     * @throws BadRequestException
     * @throws CheckAuthorizationException
     * @throws NotFoundException
     * @throws QueryException
     * @throws ServerException
     * @throws UnprocessableEntityException
     */
    public function blockUser(BlockUserRequest $request): JsonResponse
    {
        $params = $request->all();
        return $this->doRequest(function () use ($params) {
            return $this->blockService->blockUser($params);
        }, $request);
    }

    /**
     * @param UnBlockUserRequest $request
     * @return JsonResponse
     * @throws BadRequestException
     * @throws CheckAuthorizationException
     * @throws NotFoundException
     * @throws QueryException
     * @throws ServerException
     * @throws UnprocessableEntityException
     */
    public function unBlockUser(UnBlockUserRequest $request): JsonResponse
    {
        $params = $request->all();
        return $this->doRequest(function () use ($params) {
            return $this->blockService->unBlockUser($params);
        }, $request);
    }

    public function getListBlocked(ListBlockUserRequest $request): JsonResponse
    {
        $params = $request->all();
        return $this->getData(function () use ($params) {
            return $this->blockService->getListBlocked($params);
        }, $request);
    }
}
