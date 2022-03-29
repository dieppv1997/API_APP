<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Repositories;

use App\Interfaces\Repositories\ProvinceRepositoryInterface;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Models\Province;

/**
 * Class ProvinceRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class ProvinceRepositoryEloquent extends BaseRepository implements ProvinceRepositoryInterface
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Province::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    public function getList($langCode)
    {
        return $this->model->join('province_translations', 'provinces.id', '=', 'province_translations.province_id')
            ->where('province_translations.lang_code', $langCode)
            ->get();
    }
}
