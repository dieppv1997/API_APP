<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Repositories;

use App\Interfaces\Repositories\TagRepositoryInterface;
use App\Models\Tag;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class TagRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class TagRepositoryEloquent extends BaseRepository implements TagRepositoryInterface
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Tag::class;
    }
    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    public function searchTagByName($keySearch)
    {
        $orderRaw = "case when name = '?' then 1
                when name like '?%' then 2
                when name like '%?%' then 3
                when name like '%?' then 4
                end";
        return $this->model->select('id', 'name')
            ->where('name', 'like', '%'.$keySearch.'%')
            ->where('deleted_at', null)
            ->orderByRaw($orderRaw, [$keySearch, $keySearch, $keySearch, $keySearch])
            ->get();
    }
}
