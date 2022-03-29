<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class NotificationTemplate extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'description',
        'name',
        'entity_type',
        'setting_id',
    ];

    /**
     * @return HasOne
     */
    public function notificationTemplateTranslation(): HasOne
    {
        return $this->hasOne(NotificationTemplateTranslation::class)
            ->where('lang_code', '=', app()->getLocale());
    }

    /**
     * @return HasOne
     */
    public function notificationSetting(): HasOne
    {
        return $this->hasOne(Setting::class, 'id', 'setting_id');
    }
}
