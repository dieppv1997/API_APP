<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Repositories;

use App\Enums\Posts\PostStatusEnum;
use App\Enums\Users\FollowingStatusEnum;
use App\Enums\Users\UserBannedEnum;
use App\Enums\Users\UserPublicStatusEnum;
use App\Interfaces\Repositories\PostRepositoryInterface;
use App\Models\Post;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class PostRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class PostRepositoryEloquent extends BaseRepository implements PostRepositoryInterface
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Post::class;
    }
    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    /**
     * @param int|array $tagId
     * @return mixed
     */
    public function getPostsByTag($tagId)
    {
        return $this->model
            ->select(['posts.id', 'posts.image', 'posts.user_id'])
            ->leftJoin('post_tags', 'posts.id', '=', 'post_tags.post_id')
            ->join('users', 'posts.user_id', '=', 'users.id')
            ->join('post_published_sequences', 'post_published_sequences.post_id', '=', 'posts.id')
            ->where([
                'posts.status' => PostStatusEnum::PUBLISHED,
                'users.is_banned' => UserBannedEnum::USER_NOT_BANNED,
            ])
            ->whereNull('users.deleted_at')
            ->where(function ($query) use ($tagId) {
                if (is_array($tagId)) {
                    $query->whereIn('post_tags.tag_id', $tagId);
                } else {
                    $query->where(['post_tags.tag_id' => $tagId]);
                }
            })
            ->where(function ($query) {
                $query->where(['users.is_public' => UserPublicStatusEnum::PUBLIC_STATUS])
                    ->orWhere(['users.id' => Auth::id()])
                    ->orWhere(function ($query) {
                        $query->where(['users.is_public' => UserPublicStatusEnum::PRIVATE_STATUS])
                            ->whereIn('posts.user_id', function ($query) {
                                return $query->select('following_id')
                                    ->from('user_follows')
                                    ->where('user_id', Auth::id())
                                    ->where('status', FollowingStatusEnum::FOLLOWING);
                            });
                    });
            })
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('user_blocks')
                    ->where(function ($query) {
                        $query->where(function ($query) {
                            $query->whereRaw('user_blocks.block_user_id = posts.user_id')
                                ->where('user_blocks.user_id', '=', Auth::id());
                        })
                            ->orWhere(function ($query) {
                                $query->whereRaw('user_blocks.user_id = posts.user_id')
                                    ->where('user_blocks.block_user_id', '=', Auth::id());
                            });
                    });
            })
            ->distinct('posts.id')
            ->orderBy('post_published_sequences.id', 'DESC');
    }

    /**
     * @param int|array $tagId
     * @param $post
     * @return mixed
     */
    public function getPostsByTagFromPost($tagId, $post)
    {
        $condition = [
            'posts.status' => PostStatusEnum::PUBLISHED,
            'users.is_banned' => UserBannedEnum::USER_NOT_BANNED
        ];
        if (!empty($post->publishedSequence)) {
            $condition[] = ['post_published_sequences.id', '<=', $post->publishedSequence->id];
        }
        return $this->model
            ->select([
                'posts.id', 'posts.caption', 'posts.image', 'posts.place_id', 'posts.published_at', 'posts.user_id',
                'users.nickname', 'users.avatar_image',
                'places.google_place_id', 'places.place_name', 'places.place_address', 'places.latitude',
                'places.longitude'
            ])
            ->leftJoin('post_tags', 'posts.id', '=', 'post_tags.post_id')
            ->join('users', 'posts.user_id', '=', 'users.id')
            ->join('post_published_sequences', 'post_published_sequences.post_id', '=', 'posts.id')
            ->leftJoin('places', 'posts.place_id', '=', 'places.id')
            ->where($condition)
            ->whereNull('users.deleted_at')
            ->where(function ($query) use ($tagId) {
                if (is_array($tagId)) {
                    $query->whereIn('post_tags.tag_id', $tagId);
                } else {
                    $query->where(['post_tags.tag_id' => $tagId]);
                }
            })
            ->where(function ($query) {
                $query->where(['users.is_public' => UserPublicStatusEnum::PUBLIC_STATUS])
                    ->orWhere(['users.id' => Auth::id()])
                    ->orWhere(function ($query) {
                        $query->where(['users.is_public' => UserPublicStatusEnum::PRIVATE_STATUS])
                            ->whereIn('posts.user_id', function ($query) {
                                return $query->select('following_id')
                                    ->from('user_follows')
                                    ->where('user_id', Auth::id())
                                    ->where('status', FollowingStatusEnum::FOLLOWING);
                            });
                    });
            })
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('user_blocks')
                    ->where(function ($query) {
                        $query->where(function ($query) {
                            $query->whereRaw('user_blocks.block_user_id = posts.user_id')
                                ->where('user_blocks.user_id', '=', Auth::id());
                        })
                            ->orWhere(function ($query) {
                                $query->whereRaw('user_blocks.user_id = posts.user_id')
                                    ->where('user_blocks.block_user_id', '=', Auth::id());
                            });
                    });
            })
            ->with(['liked', 'tags'])
            ->distinct('posts.id')
            ->withCount('likes')
            ->withCount([
                'postComments' => function ($query) {
                    $query->whereNotExists(function ($query) {
                        $query->select(DB::raw(1))
                            ->from('user_blocks')
                            ->where(function ($query) {
                                $query->where(function ($query) {
                                    $query->whereRaw('user_blocks.block_user_id = comments.user_id')
                                        ->where('user_blocks.user_id', '=', Auth::id());
                                })
                                    ->orWhere(function ($query) {
                                        $query->whereRaw('user_blocks.user_id = comments.user_id')
                                            ->where('user_blocks.block_user_id', '=', Auth::id());
                                    });
                            });
                    });
                }
            ])
            ->orderBy('post_published_sequences.id', 'DESC');
    }

    /**
     * @return mixed
     */
    public function getListNewest()
    {
        return $this->model
            ->select(['posts.id', 'posts.image', 'posts.user_id'])
            ->join('users', 'posts.user_id', '=', 'users.id')
            ->join('post_published_sequences', 'post_published_sequences.post_id', '=', 'posts.id')
            ->where([
                'posts.status' => PostStatusEnum::PUBLISHED,
                'posts.deleted_at' => null,
                'users.is_public' => UserPublicStatusEnum::PUBLIC_STATUS,
                'users.is_banned' => UserBannedEnum::USER_NOT_BANNED,
            ])
            ->whereNull('users.deleted_at')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('user_blocks')
                    ->where(function ($query) {
                        $query->where(function ($query) {
                            $query->whereRaw('user_blocks.block_user_id = posts.user_id')
                                ->where('user_blocks.user_id', '=', Auth::id());
                        })
                            ->orWhere(function ($query) {
                                $query->whereRaw('user_blocks.user_id = posts.user_id')
                                    ->where('user_blocks.block_user_id', '=', Auth::id());
                            });
                    });
            })
            ->orderBy('post_published_sequences.id', 'DESC');
    }

    /**
     * @param $post
     * @return mixed
     */
    public function getListNewestFromPost($post)
    {
        $condition = [
            'posts.status' => PostStatusEnum::PUBLISHED,
            'users.is_public' => UserPublicStatusEnum::PUBLIC_STATUS,
            'users.is_banned' => UserBannedEnum::USER_NOT_BANNED
        ];
        if (!empty($post->publishedSequence)) {
            $condition[] = ['post_published_sequences.id', '<=', $post->publishedSequence->id];
        }
        return $this->model
            ->select([
                'posts.id', 'posts.caption', 'posts.image', 'posts.place_id', 'posts.published_at', 'posts.user_id',
                'users.nickname', 'users.avatar_image',
                'places.google_place_id', 'places.place_name', 'places.place_address', 'places.latitude',
                'places.longitude'
            ])
            ->join('users', 'posts.user_id', '=', 'users.id')
            ->join('post_published_sequences', 'post_published_sequences.post_id', '=', 'posts.id')
            ->leftJoin('places', 'posts.place_id', '=', 'places.id')
            ->where($condition)
            ->whereNull('users.deleted_at')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('user_blocks')
                    ->where(function ($query) {
                        $query->where(function ($query) {
                            $query->whereRaw('user_blocks.block_user_id = posts.user_id')
                                ->where('user_blocks.user_id', '=', Auth::id());
                        })
                            ->orWhere(function ($query) {
                                $query->whereRaw('user_blocks.user_id = posts.user_id')
                                    ->where('user_blocks.block_user_id', '=', Auth::id());
                            });
                    });
            })
            ->with(['liked', 'tags'])
            ->withCount(['likes'])
            ->withCount([
                'postComments' => function ($query) {
                    $query->whereNotExists(function ($query) {
                        $query->select(DB::raw(1))
                            ->from('user_blocks')
                            ->where(function ($query) {
                                $query->where(function ($query) {
                                    $query->whereRaw('user_blocks.block_user_id = comments.user_id')
                                        ->where('user_blocks.user_id', '=', Auth::id());
                                })
                                    ->orWhere(function ($query) {
                                        $query->whereRaw('user_blocks.user_id = comments.user_id')
                                            ->where('user_blocks.block_user_id', '=', Auth::id());
                                    });
                            });
                    });
                }
            ])
            ->orderBy('post_published_sequences.id', 'DESC');
    }

    public function getPostById($postId, $onlyPublished = true)
    {
        $conditions = [
            'posts.id' => $postId,
            'users.is_banned' => UserBannedEnum::USER_NOT_BANNED
        ];
        if ($onlyPublished) {
            $conditions['posts.status'] = PostStatusEnum::PUBLISHED;
        }
        return $this->model
            ->select(['posts.id', 'posts.caption', 'posts.image', 'posts.place_id', 'posts.published_at',
                'posts.user_id', 'posts.original_image', 'users.nickname', 'users.avatar_image',
            ])
            ->join('users', 'posts.user_id', '=', 'users.id')
            ->leftJoin('places', 'posts.place_id', '=', 'places.id')
            ->where($conditions)
            ->whereNull('users.deleted_at')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('user_blocks')
                    ->where(function ($query) {
                        $query->where(function ($query) {
                            $query->whereRaw('user_blocks.block_user_id = posts.user_id')
                                ->where('user_blocks.user_id', '=', Auth::id());
                        })
                            ->orWhere(function ($query) {
                                $query->whereRaw('user_blocks.user_id = posts.user_id')
                                    ->where('user_blocks.block_user_id', '=', Auth::id());
                            });
                    });
            })
            ->with(['tags', 'liked'])
            ->withCount('likes')
            ->withCount([
                'postComments' => function ($query) {
                    $query->whereNotExists(function ($query) {
                        $query->select(DB::raw(1))
                            ->from('user_blocks')
                            ->where(function ($query) {
                                $query->where(function ($query) {
                                    $query->whereRaw('user_blocks.block_user_id = comments.user_id')
                                        ->where('user_blocks.user_id', '=', Auth::id());
                                })
                                    ->orWhere(function ($query) {
                                        $query->whereRaw('user_blocks.user_id = comments.user_id')
                                            ->where('user_blocks.block_user_id', '=', Auth::id());
                                    });
                            });
                    });
                }
            ]);
    }

    public function getPostsByFollowingUser(int $currentUserId)
    {
        return $this->model
            ->select([
                'posts.id', 'posts.caption', 'posts.image', 'posts.place_id', 'posts.published_at', 'posts.user_id',
                'users.nickname', 'users.avatar_image',
                'places.google_place_id', 'places.place_name', 'places.place_address', 'places.latitude',
                'places.longitude'
            ])
            ->join('user_follows', 'posts.user_id', '=', 'user_follows.following_id')
            ->join('users', 'posts.user_id', '=', 'users.id')
            ->join('post_published_sequences', 'post_published_sequences.post_id', '=', 'posts.id')
            ->leftJoin('places', 'posts.place_id', '=', 'places.id')
            ->with(['liked', 'tags'])
            ->withCount('likes')
            ->withCount([
                'postComments' => function ($query) {
                    $query->whereNotExists(function ($query) {
                        $query->select(DB::raw(1))
                            ->from('user_blocks')
                            ->where(function ($query) {
                                $query->where(function ($query) {
                                    $query->whereRaw('user_blocks.block_user_id = comments.user_id')
                                        ->where('user_blocks.user_id', '=', Auth::id());
                                })
                                    ->orWhere(function ($query) {
                                        $query->whereRaw('user_blocks.user_id = comments.user_id')
                                            ->where('user_blocks.block_user_id', '=', Auth::id());
                                    });
                            });
                    });
                }
            ])
            ->where([
                'posts.status' => PostStatusEnum::PUBLISHED,
                'user_follows.status' => FollowingStatusEnum::FOLLOWING,
                'user_follows.user_id' => $currentUserId,
                'users.is_banned' => UserBannedEnum::USER_NOT_BANNED,
            ])
            ->whereNull('users.deleted_at')
            ->orderBy('post_published_sequences.id', 'DESC');
    }

    public function getPostForEdit($postId)
    {
        return $this->model
            ->select(['posts.id', 'posts.caption', 'posts.image', 'posts.place_id', 'posts.published_at',
                'posts.user_id'])
            ->leftJoin('places', 'posts.place_id', '=', 'places.id')
            ->where([
                'posts.id' => $postId
            ]);
    }

    public function getDraftOfPostByUser()
    {
        return $this->model
            ->select(['posts.id', 'posts.caption', 'posts.image', 'posts.created_at',])
            ->where([
                'user_id' => Auth::id(),
                'status' => PostStatusEnum::DRAFT,
            ]);
    }

    public function getPostByUserId($userId)
    {
        return $this->model
            ->select(['posts.id', 'posts.caption', 'posts.image', 'posts.published_at',])
            ->join('post_published_sequences', 'post_published_sequences.post_id', '=', 'posts.id')
            ->where([
                'user_id' => $userId,
                'status' => PostStatusEnum::PUBLISHED,
            ])
            ->orderBy('post_published_sequences.id', 'DESC');
    }

    public function getPostByUserFromPost(int $userId, $post)
    {
        $condition = [
            'users.id' => $userId,
            'posts.status' => PostStatusEnum::PUBLISHED
        ];
        if (!empty($post->publishedSequence)) {
            $condition[] = ['post_published_sequences.id', '<=', $post->publishedSequence->id];
        }
        return $this->model
            ->select([
                'posts.id', 'posts.caption', 'posts.image', 'posts.place_id', 'posts.published_at', 'posts.user_id',
                'users.nickname', 'users.avatar_image',
                'places.google_place_id', 'places.place_name', 'places.place_address', 'places.latitude',
                'places.longitude'
            ])
            ->join('users', 'posts.user_id', '=', 'users.id')
            ->join('post_published_sequences', 'post_published_sequences.post_id', '=', 'posts.id')
            ->leftJoin('places', 'posts.place_id', '=', 'places.id')
            ->where($condition)
            ->whereNull('users.deleted_at')
            ->with(['liked', 'tags'])
            ->withCount('likes')
            ->withCount([
                'postComments' => function ($query) {
                    $query->whereNotExists(function ($query) {
                        $query->select(DB::raw(1))
                            ->from('user_blocks')
                            ->where(function ($query) {
                                $query->where(function ($query) {
                                    $query->whereRaw('user_blocks.block_user_id = comments.user_id')
                                        ->where('user_blocks.user_id', '=', Auth::id());
                                })
                                    ->orWhere(function ($query) {
                                        $query->whereRaw('user_blocks.user_id = comments.user_id')
                                            ->where('user_blocks.block_user_id', '=', Auth::id());
                                    });
                            });
                    });
                }
            ])
            ->orderBy('post_published_sequences.id', 'DESC');
    }

    public function getPostLikedOfUser(int $userId)
    {
        return $this->model
            ->select([
                'posts.id', 'posts.caption', 'posts.image', 'posts.place_id', 'posts.published_at', 'posts.user_id',
                'post_likes.id as liked_id',
            ])
            ->join('post_likes', 'posts.id', '=', 'post_likes.post_id')
            ->join('users', 'users.id', '=', 'posts.user_id')
            ->where([
                'posts.status' => PostStatusEnum::PUBLISHED,
                'post_likes.user_id' => $userId,
                'users.is_banned' => UserBannedEnum::USER_NOT_BANNED,
            ])
            ->whereNull('users.deleted_at')
            ->where(function ($query) use ($userId) {
                $query->where(['users.is_public' => UserPublicStatusEnum::PUBLIC_STATUS])
                    ->orWhere(['users.id' => Auth::id()])
                    ->orWhere(function ($query) {
                        $query->where(['users.is_public' => UserPublicStatusEnum::PRIVATE_STATUS])
                            ->whereIn('posts.user_id', function ($query) {
                                return $query->select('following_id')
                                    ->from('user_follows')
                                    ->where('user_id', Auth::id())
                                    ->where('status', FollowingStatusEnum::FOLLOWING);
                            });
                    });
            })
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('user_blocks')
                    ->where(function ($query) {
                        $query->where(function ($query) {
                            $query->whereRaw('user_blocks.block_user_id = posts.user_id')
                                ->where('user_blocks.user_id', '=', Auth::id());
                        })
                            ->orWhere(function ($query) {
                                $query->whereRaw('user_blocks.user_id = posts.user_id')
                                    ->where('user_blocks.block_user_id', '=', Auth::id());
                            });
                    });
            })
            ->orderBy('post_likes.id', 'DESC');
    }

    public function getPostLikedOfUserFromId(int $userId, int $likedId)
    {
        return $this->model
            ->select([
                'posts.id', 'posts.caption', 'posts.image', 'posts.place_id', 'posts.published_at', 'posts.user_id',
                'users.nickname', 'users.avatar_image',
                'places.google_place_id', 'places.place_name', 'places.place_address', 'places.latitude',
                'places.longitude'
            ])
            ->join('post_likes', 'posts.id', '=', 'post_likes.post_id')
            ->join('users', 'posts.user_id', '=', 'users.id')
            ->leftJoin('places', 'posts.place_id', '=', 'places.id')
            ->with(['liked', 'tags'])
            ->withCount('likes')
            ->withCount([
                'postComments' => function ($query) {
                    $query->whereNotExists(function ($query) {
                        $query->select(DB::raw(1))
                            ->from('user_blocks')
                            ->where(function ($query) {
                                $query->where(function ($query) {
                                    $query->whereRaw('user_blocks.block_user_id = comments.user_id')
                                        ->where('user_blocks.user_id', '=', Auth::id());
                                })
                                    ->orWhere(function ($query) {
                                        $query->whereRaw('user_blocks.user_id = comments.user_id')
                                            ->where('user_blocks.block_user_id', '=', Auth::id());
                                    });
                            });
                    });
                }
            ])
            ->where([
                'users.is_banned' => UserBannedEnum::USER_NOT_BANNED,
                'posts.status' => PostStatusEnum::PUBLISHED,
                'post_likes.user_id' => $userId,
                ['post_likes.id', '<=', $likedId]
            ])
            ->whereNull('users.deleted_at')
            ->where(function ($query) {
                $query->where(['users.is_public' => UserPublicStatusEnum::PUBLIC_STATUS])
                    ->orWhere(['users.id' => Auth::id()])
                    ->orWhere(function ($query) {
                        $query->where(['users.is_public' => UserPublicStatusEnum::PRIVATE_STATUS])
                            ->whereIn('posts.user_id', function ($query) {
                                return $query->select('following_id')
                                    ->from('user_follows')
                                    ->where('user_id', Auth::id())
                                    ->where('status', FollowingStatusEnum::FOLLOWING);
                            });
                    });
            })
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('user_blocks')
                    ->where(function ($query) {
                        $query->where(function ($query) {
                            $query->whereRaw('user_blocks.block_user_id = posts.user_id')
                                ->where('user_blocks.user_id', '=', Auth::id());
                        })
                            ->orWhere(function ($query) {
                                $query->whereRaw('user_blocks.user_id = posts.user_id')
                                    ->where('user_blocks.block_user_id', '=', Auth::id());
                            });
                    });
            })
            ->orderBy('post_likes.id', 'DESC');
    }

    /**
     * @param $postId
     * @return Builder|Model|object|null
     */
    public function getPostWithAuthorById($postId)
    {
        return $this->model->with('author')
            ->where([
                'id' => $postId,
                'status' => PostStatusEnum::PUBLISHED
            ])
            ->first();
    }
}
