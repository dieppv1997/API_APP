<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Models;

use App\Enums\Common\CommonEnum;
use App\Enums\Users\UserBannedEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class Post extends Model implements Transformable
{
    use TransformableTrait, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'user_id',
        'image',
        'original_image',
        'caption',
        'place_id',
        'published_at',
        'status'
    ];

    /**
     * Get the place for the post.
     * @return HasOne
     */
    public function place(): HasOne
    {
        return $this->hasOne('App\Models\Place', 'id', 'place_id');
    }

    /**
     * Get the comments for the post.
     * @return HasMany
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)->where('parent_id', 0);
    }

    /**
     * @return HasManyThrough
     */
    public function postComments(): HasManyThrough
    {
        return $this->hasManyThrough(User::class, Comment::class, 'post_id', 'id', 'id', 'user_id')
            ->where('users.is_banned', UserBannedEnum::USER_NOT_BANNED)
            ->where('comments.hide_by_banned', CommonEnum::BOOL_FALSE);
    }

    /**
     * @return HasManyThrough
     */
    public function likes(): HasManyThrough
    {
        return $this->hasManyThrough(User::class, PostLike::class, 'post_id', 'id', 'id', 'user_id')
            ->where('users.is_banned', UserBannedEnum::USER_NOT_BANNED);
    }

    /**
     * @return HasManyThrough
     */
    public function tags(): HasManyThrough
    {
        return $this->hasManyThrough(Tag::class, PostTag::class, 'post_id', 'id', 'id', 'tag_id');
    }

    /**
     * @return HasOne
     */
    public function liked(): HasOne
    {
        return $this->hasOne(PostLike::class)->where('user_id', '=', Auth::id());
    }

    /**
     * @return HasOne
     */
    public function author(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    /**
     * @return HasOne
     */
    public function publishedSequence(): HasOne
    {
        return $this->hasOne(PostPublishedSequence::class);
    }

    /**
     * Get all of post's notifications.
     * @return MorphMany
     */
    public function notification(): MorphMany
    {
        return $this->morphMany(Notification::class, 'notifiable');
    }
}
