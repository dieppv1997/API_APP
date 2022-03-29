<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class OfficialNotification extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'title',
        'web_link',
        'start_show_date',
        'end_show_date',
    ];

    /**
     * Get all of the user's notifications.
     */
    public function notification(): MorphMany
    {
        return $this->morphMany(Notification::class, 'notifiable')
            ->where('receiver_id', Auth::id());
    }
}
