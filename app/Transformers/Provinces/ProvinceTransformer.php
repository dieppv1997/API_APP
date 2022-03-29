<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Transformers\Provinces;

use League\Fractal\TransformerAbstract;
use App\Models\Province;

class ProvinceTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     * @param Province $province
     * @return array
     */
    public function transform(Province $province)
    {
        return [
            'id' => $province['id'],
            'name' => !empty($province['name']) ? $province['name']:null
        ];
    }
}
