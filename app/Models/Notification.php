<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Models;

use App\Enums\Notifications\NotificationTemplateNameEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notification extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'notification_template_id',
        'actor_id',
        'receiver_id',
        'notifiable_id',
        'notifiable_type',
        'post_id',
        'read_at',
        'created_at',
    ];

    /**
     * Get the parent notifiable model
     */
    public function notifiable(): MorphTo
    {
        return $this->morphTo();
    }

    public function notificationTemplates(): HasOne
    {
        return $this->hasOne(NotificationTemplate::class, 'id', 'notification_template_id');
    }

    /**
     * @return HasOne
     */
    public function actor(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'actor_id');
    }

    public function templatesTranslations(): HasOneThrough
    {
        return $this->hasOneThrough(
            NotificationTemplateTranslation::class,
            NotificationTemplate::class,
            'id',
            'notification_template_id',
            'notification_template_id'
        );
    }

    public function post(): HasOne
    {
        return $this->hasOne(Post::class, 'id', 'post_id')->withTrashed();
    }
}
