<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSetting extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'setting_id',
        'value'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
