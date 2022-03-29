<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Repositories;

use App\Enums\Posts\PostStatusEnum;
use App\Enums\Recommendation\RecommendLimitEnum;
use App\Enums\Users\UserBannedEnum;
use App\Enums\Users\UserPublicStatusEnum;
use App\Interfaces\Repositories\PostTagRepositoryInterface;
use App\Models\PostTag;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class PostTagRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class PostTagRepositoryEloquent extends BaseRepositoryEloquent implements PostTagRepositoryInterface
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return PostTag::class;
    }
    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    public function getListRecommendTag()
    {
        return $this->model->select('post_tags.tag_id', 'tags.name as name', DB::raw('COUNT(*) as total_post'))
            ->join('posts', 'posts.id', '=', 'post_tags.post_id')
            ->join('tags', 'tags.id', '=', 'post_tags.tag_id')
            ->where([
                'posts.status' => PostStatusEnum::PUBLISHED
            ])
            ->groupBy('post_tags.tag_id')
            ->orderBy('total_post', 'DESC')
            ->limit(RecommendLimitEnum::LIMIT_RECOMMEND_TAG)
            ->get();
    }

    public function getPostOfRecommendTags($listRecommendTag)
    {
        return $this->model->select('post_tags.post_id as id', 'posts.image as thumbnail', 'posts.user_id')
            ->join('posts', 'posts.id', '=', 'post_tags.post_id')
            ->join('users', 'posts.user_id', '=', 'users.id')
            ->join('post_published_sequences', 'post_published_sequences.post_id', '=', 'posts.id')
            ->where([
                'tag_id' => $listRecommendTag['tag_id'],
                'users.is_public' => UserPublicStatusEnum::PUBLIC_STATUS,
                'users.is_banned' => UserBannedEnum::USER_NOT_BANNED,
                'posts.deleted_at' => null
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
            ->limit(RecommendLimitEnum::LIMIT_POST_OF_RECOMMEND_TAG)
            ->orderBy('post_published_sequences.id', 'DESC')
            ->get();
    }
}
