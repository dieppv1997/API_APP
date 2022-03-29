<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class ProvinceTranslation.
 *
 * @package namespace App\Models;
 */
class ProvinceTranslation extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'province_id',
        'lang_code',
        'name',
        'created_at',
        'updated_at',
    ];
}
