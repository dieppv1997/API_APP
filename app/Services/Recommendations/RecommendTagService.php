<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Services\Recommendations;

use App\Interfaces\Services\Recommendations\RecommendTagServiceInterface;
use App\Repositories\PostTagRepositoryEloquent;
use App\Services\BaseService;
use App\Transformers\CustomSerializer;
use App\Transformers\Posts\PostRecommendationTransformer;

class RecommendTagService extends BaseService implements RecommendTagServiceInterface
{
    public $postTagRepository;

    public function __construct(PostTagRepositoryEloquent $postTagRepository)
    {
        $this->postTagRepository = $postTagRepository;
    }

    public function getListRecommendTag($params)
    {
        $listRecommendTags = $this->postTagRepository->getListRecommendTag()->toArray();

        foreach ($listRecommendTags as $key => $listRecommendTag) {
            $postOfTags = $this->postTagRepository->getPostOfRecommendTags($listRecommendTag)->toArray();
            $formattedData = fractal($postOfTags, new PostRecommendationTransformer(), CustomSerializer::class);
            $listRecommendTags[$key]['posts'] = $formattedData;
            unset($listRecommendTags[$key]['total_post']);
        }
        return [
            'data' => $listRecommendTags
        ];
    }
}
