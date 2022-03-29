<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Models;

use App\Enums\Common\CommonEnum;
use App\Enums\Users\RegisterTypeEnum;
use App\Enums\Users\UserStatusEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'nickname',
        'email',
        'password',
        'avatar_image',
        'cover_image',
        'province_id',
        'favorite_shop',
        'favorite_place',
        'intro',
        'gender',
        'birthday',
        'activated',
        'is_public',
        'is_banned',
        'device_id',
        'register_type',
        'email_verified_at',
        'accept_rule_at',
    ];

    /**
     * Scope a query to only include not banned users.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeNotBanned(Builder $query): Builder
    {
        return $query->where('is_banned', CommonEnum::BOOL_FALSE);
    }

    /**
     * Scope a query to only include popular users.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where(function ($query) {
            $query->where('activated', UserStatusEnum::ACTIVE)
                ->orWhere('register_type', RegisterTypeEnum::REGISTER_NICKNAME);
        });
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Return record of user_follows table if user is following by logged in user
     * @return HasOne
     */
    public function following(): HasOne
    {
        return $this->hasOne(UserFollow::class, 'following_id')->where('user_id', Auth::id());
    }

    /**
     * Return record of user_blocks table if user is block by logged in user
     * @return HasOne
     */
    public function blockByLoggedInUser(): HasOne
    {
        return $this->hasOne(UserBlock::class, 'block_user_id')->where('user_id', Auth::id());
    }

    /**
     * Return record of user_blocks table if user block logged in user
     * @return HasOne
     */
    public function blockLoggedInUser(): HasOne
    {
        return $this->hasOne(UserBlock::class, 'user_id')->where('block_user_id', Auth::id());
    }

    /**
     * Get the place for the post.
     * @return HasOneThrough
     */
    public function province(): HasOneThrough
    {
        return $this->hasOneThrough(
            ProvinceTranslation::class,
            Province::class,
            'id',
            'province_id',
            'province_id'
        );
    }

    public function notification()
    {
        return $this->morphMany(Notification::class, 'notifiable');
    }
}
