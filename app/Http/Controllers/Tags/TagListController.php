<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Http\Controllers\Tags;

use App\Exceptions\BadRequestException;
use App\Exceptions\CheckAuthorizationException;
use App\Exceptions\NotFoundException;
use App\Exceptions\QueryException;
use App\Exceptions\ServerException;
use App\Exceptions\UnprocessableEntityException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tags\SearchTagByNameRequest;
use App\Http\Requests\Recommendation\ListRecommendTagRequest;
use App\Interfaces\Services\Recommendations\RecommendTagServiceInterface;
use App\Interfaces\Services\Tags\TagServiceInterface;
use Illuminate\Http\JsonResponse;

class TagListController extends Controller
{
    protected $recommendTagService;
    protected $tagService;

    public function __construct(
        RecommendTagServiceInterface $recommendTagService,
        TagServiceInterface $tagService
    ) {
        $this->recommendTagService = $recommendTagService;
        $this->tagService = $tagService;
    }

    /**
     * @param ListRecommendTagRequest $request
     * @return JsonResponse
     * @throws BadRequestException
     * @throws CheckAuthorizationException
     * @throws NotFoundException
     * @throws QueryException
     * @throws ServerException
     * @throws UnprocessableEntityException
     */
    public function getListRecommendTag(ListRecommendTagRequest $request): JsonResponse
    {
        $params = $request->all();
        return $this->doRequest(function () use ($params) {
            return $this->recommendTagService->getListRecommendTag($params);
        }, $request);
    }

    public function searchTagByName(SearchTagByNameRequest $request)
    {
        $params = $request->all();
        return $this->getData(function () use ($params) {
            return $this->tagService->searchTagByName($params);
        }, $request);
    }
}
