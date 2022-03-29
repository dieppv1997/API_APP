<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Models;

use App\Enums\Common\CommonEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class Comment extends Model implements Transformable
{
    use TransformableTrait, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'post_id',
        'user_id',
        'parent_id',
        'content',
        'hide_by_banned'
    ];

    /**
     * Scope a query to only include not banned users.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeNotBanned(Builder $query): Builder
    {
        return $query->where('hide_by_banned', CommonEnum::BOOL_FALSE);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'post_id');
    }

    /**
     * Get the replies for the comment.
     */
    public function replies(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    /**
     * Get the replies for the comment.
     */
    public function parent(): HasOne
    {
        return $this->hasOne(Comment::class, 'id', 'parent_id');
    }

    public function liked(): HasOne
    {
        return $this->hasOne(CommentLike::class)->where('user_id', '=', Auth::id());
    }

    public function like(): HasManyThrough
    {
        return $this->hasManyThrough(User::class, CommentLike::class, 'comment_id', 'id', 'id', 'user_id')
            ->where('users.is_banned', 0);
    }

    /**
     * Get all of the comment's notifications.
     */
    public function notification(): MorphMany
    {
        return $this->morphMany(Notification::class, 'notifiable');
    }
}
