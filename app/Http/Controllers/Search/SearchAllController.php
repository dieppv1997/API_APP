<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Http\Controllers\Search;

use App\Exceptions\CheckAuthenticationException;
use App\Exceptions\CheckAuthorizationException;
use App\Exceptions\NotFoundException;
use App\Exceptions\QueryException;
use App\Exceptions\ServerException;
use App\Exceptions\UnprocessableEntityException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Search\SearchAllPostByKeyFromIdRequest;
use App\Http\Requests\Search\SearchByKeyRequest;
use App\Interfaces\Services\Search\SearchServiceInterface;
use Illuminate\Http\JsonResponse;

class SearchAllController extends Controller
{
    protected $searchService;

    public function __construct(SearchServiceInterface $searchService)
    {
        $this->searchService = $searchService;
    }

    /**
     * @param SearchByKeyRequest $request
     * @return JsonResponse
     * @throws CheckAuthenticationException
     * @throws CheckAuthorizationException
     * @throws NotFoundException
     * @throws QueryException
     * @throws ServerException
     * @throws UnprocessableEntityException
     */
    public function searchAllByKey(SearchByKeyRequest $request): JsonResponse
    {
        $params = $request->all();
        return $this->getData(function () use ($params) {
            return $this->searchService->searchAllByKey($params);
        }, $request);
    }

    /**
     * @param SearchByKeyRequest $request
     * @return JsonResponse
     * @throws CheckAuthenticationException
     * @throws CheckAuthorizationException
     * @throws NotFoundException
     * @throws QueryException
     * @throws ServerException
     * @throws UnprocessableEntityException
     */
    public function searchAllUserByKey(SearchByKeyRequest $request): JsonResponse
    {
        $params = $request->all();
        return $this->getData(function () use ($params) {
            return $this->searchService->searchAllUserByKey($params);
        }, $request);
    }

    /**
     * @param SearchAllPostByKeyFromIdRequest $request
     * @return JsonResponse
     * @throws CheckAuthenticationException
     * @throws CheckAuthorizationException
     * @throws NotFoundException
     * @throws QueryException
     * @throws ServerException
     * @throws UnprocessableEntityException
     */
    public function searchAllPostByKey(SearchAllPostByKeyFromIdRequest $request): JsonResponse
    {
        $params = $request->all();
        return $this->getData(function () use ($params) {
            return $this->searchService->searchAllPostByKey($params);
        }, $request);
    }
}
