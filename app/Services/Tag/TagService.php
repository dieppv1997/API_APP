<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Services\Tag;

use App\Interfaces\Repositories\TagRepositoryInterface;
use App\Interfaces\Services\Tags\TagServiceInterface;
use App\Services\BaseService;
use App\Transformers\Tags\SearchTagTransformer;

class TagService extends BaseService implements TagServiceInterface
{
    public $tagRepository;

    public function __construct(TagRepositoryInterface $tagRepositoryEloquent)
    {
        $this->tagRepository = $tagRepositoryEloquent;
    }

    public function searchTagByName($params)
    {
        $keySearch = $params['tagName'];
        $result = $this->tagRepository->searchTagByName($keySearch);
        return fractal($result, new SearchTagTransformer());
    }
}
