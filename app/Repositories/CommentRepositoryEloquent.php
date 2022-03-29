<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Repositories;

use App\Enums\Comments\CommentLimitEnum;
use App\Enums\Users\UserBannedEnum;
use App\Interfaces\Repositories\CommentRepositoryInterface;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class CommentRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class CommentRepositoryEloquent extends BaseRepository implements CommentRepositoryInterface
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Comment::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    public function getListRootCommentByPostId(int $postId)
    {
        return $this->model->where([
            'parent_id' => 0,
            'post_id' => $postId
        ])
            ->join('users', 'users.id', '=', 'user_id')
            ->where('users.is_banned', UserBannedEnum::USER_NOT_BANNED)
            ->where('users.deleted_at', null)
            ->whereNotExists(function ($query) {
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
            })
            ->with([
                'replies' => function ($query) {
                    return $query
                        ->join('users', 'users.id', '=', 'user_id')
                        ->where('users.is_banned', UserBannedEnum::USER_NOT_BANNED)
                        ->where('users.deleted_at', null)
                        ->whereNotExists(function ($query) {
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
                        })
                        ->with(['author', 'liked'])
                        ->withCount(['like'])
                        ->orderBy('id', 'asc');
                },
                'author',
                'liked'
            ])
            ->withCount(['like'])
            ->orderBy('id', 'asc');
    }

    public function getListRepliesByCommentIdAndPostId(int $commentId, int $postId)
    {
        return $this->model->where([
            'parent_id' => $commentId,
            'post_id' => $postId
        ])
            ->join('users', 'users.id', '=', 'user_id')
            ->where('users.is_banned', UserBannedEnum::USER_NOT_BANNED)
            ->where('users.deleted_at', null)
            ->whereNotExists(function ($query) {
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
            })
            ->withCount(['like'])
            ->with('author')
            ->withCount(['like'])
            ->orderBy('id', 'asc');
    }
}
