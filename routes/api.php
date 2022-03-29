<?php

use App\Http\Controllers\Notifications\NotificationController;
use App\Http\Controllers\Search\SearchAllController;
use App\Http\Controllers\Tags\TagListController;
use App\Http\Controllers\Posts\PostController;
use App\Http\Controllers\Users\ChangeEmailController;
use App\Http\Controllers\Users\UserSettingController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Provinces\ProvinceController;
use App\Http\Controllers\Users\UserController;
use App\Http\Controllers\Posts\PostListController;
use App\Http\Controllers\Comments\CommentController;
use App\Http\Controllers\Users\FollowingController;
use App\Http\Controllers\Users\ResetPasswordController;
use App\Http\Controllers\WebView\WebViewController;
use App\Http\Controllers\App\AppSettingController;
use App\Http\Controllers\Users\BlockController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
/* Public routes */
Route::get('/province', [ProvinceController::class, 'getList']);

Route::group([
    'prefix' => 'auth',
], function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register-email', [AuthController::class, 'registerEmail']);
    Route::post('/register-nickname', [AuthController::class, 'registerByNickname']);
    Route::post('/verify-email', [AuthController::class, 'verifyEmail']);

    Route::group(['middleware'=>['auth:sanctum', 'checkBanned']], function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/register-email/nickname', [AuthController::class, 'registerEmailNickname']);
        Route::post('/verify-email/nickname', [AuthController::class, 'verifyEmailNickname']);
    });
});

Route::group([
    'prefix' => 'user',
], function () {
    Route::post('/reset-password/request', [ResetPasswordController::class, 'requestResetPassword']);
    Route::post('/reset-password/reset', [ResetPasswordController::class, 'resetPassword']);

    Route::group(['middleware'=>['auth:sanctum']], function () {
        Route::post('/', [UserController::class, 'user']);
    });

    Route::group(['middleware'=>['auth:sanctum', 'checkBanned']], function () {
        Route::post('/update-profile', [UserController::class, 'updateProfile']);
        Route::get('/blocked', [BlockController::class, 'getListBlocked']);
        Route::get('/{userId}', [UserController::class, 'profile']);
        Route::post('/change-password', [UserController::class, 'changePassword']);
        Route::put('/follow/{userId}', [FollowingController::class, 'followUser']);
        Route::delete('/follow/{userId}', [FollowingController::class, 'unFollowUser']);
        Route::put('/follow/request/{userId}', [FollowingController::class, 'approveFollow']);
        Route::delete('/follow/request/{userId}', [FollowingController::class, 'rejectFollow']);
        Route::post('/change-email/request', [ChangeEmailController::class, 'requestChangeEmail']);
        Route::post('/change-email/verify', [ChangeEmailController::class, 'verifyChangeEmail']);
        Route::get('/{userId}/followers', [UserController::class, 'getListFollowers']);
        Route::get('/{userId}/following', [UserController::class, 'getListFollowing']);
        Route::put('/block/{userId}', [BlockController::class, 'blockUser']);
        Route::delete('/block/{userId}', [BlockController::class, 'unBlockUser']);
        Route::post('/setting/notification', [UserSettingController::class, 'settingNotification']);
        Route::get('/setting/notification', [UserSettingController::class, 'getSettingNotification']);
        Route::post('/setting/privacy', [UserSettingController::class, 'settingPrivacy']);
    });
});

Route::group([
    'prefix' => 'posts',
], function () {
    Route::group(['middleware'=>['auth:sanctum', 'checkBanned']], function () {
        Route::get('/list/by-tag/{tagId}', [PostListController::class, 'listByTag']);
        Route::get('/list/by-tag/{tagId}/{postId}', [PostListController::class, 'listByTagAndId']);
        Route::get('/list/new-arrival', [PostListController::class, 'listNewest']);
        Route::get('/list/new-arrival/{postId}', [PostListController::class, 'listNewestFromId']);
        Route::get('/list/by-following', [PostListController::class, 'listByFollowingUser']);
        Route::get('/list/draft', [PostListController::class, 'listDraftByUser']);
        Route::put('/{postId}/like', [PostController::class, 'likePost']);
        Route::delete('/{postId}/unlike', [PostController::class, 'unlikePost']);
        Route::post('/', [PostController::class, 'createPost']);
        Route::get('/{postId}/comments', [CommentController::class, 'listCommentByPost']);
        Route::post('/{postId}/comments', [CommentController::class, 'postComment']);
        Route::put('/{postId}/comments/{commentId}', [CommentController::class, 'updateComment']);
        Route::delete('/{postId}/comments/{commentId}', [CommentController::class, 'deleteComment']);
        Route::get('/{postId}', [PostController::class, 'getDetail']);
        Route::post('/{postId}', [PostController::class, 'updatePost']);
        Route::delete('/{postId}', [PostController::class, 'deletePost']);
        Route::get('/edit/{postId}', [PostController::class, 'showPostForEdit']);
        Route::post('public/{postId}', [PostController::class, 'publicPost']);
        Route::get('/list/{userId}/my-post', [PostListController::class, 'listMyPost']);
        Route::get('/list/{userId}/my-post/{postId}', [PostListController::class, 'listMyPostFromPostId']);
        Route::get('/list/{userId}/liked-post', [PostListController::class, 'listPostLiked']);
        Route::get('/list/{userId}/liked-post/{likedId}', [PostListController::class, 'listPostLikedFromPostId']);
    });
});

Route::group([
    'prefix' => 'comments',
], function () {
    Route::group(['middleware'=>['auth:sanctum', 'checkBanned']], function () {
        Route::put('/{commentId}/like', [CommentController::class, 'likeComment']);
        Route::delete('/{commentId}/unlike', [CommentController::class, 'unlikeComment']);
    });
});

Route::group([
    'prefix' => 'recommendation',
], function () {
    Route::group(['middleware'=>['auth:sanctum', 'checkBanned']], function () {
        Route::get('/users', [UserController::class, 'getListRecommendUser']);
        Route::get('/tags', [TagListController::class, 'getListRecommendTag']);
    });
});

Route::group([
    'prefix' => 'tag',
], function () {
    Route::group(['middleware'=>['auth:sanctum', 'checkBanned']], function () {
        Route::get('/', [TagListController::class, 'searchTagByName']);
    });
});


Route::group([
    'prefix' => 'web-view',
], function () {
    Route::group(['middleware'=>['auth:sanctum', 'checkBanned']], function () {
        Route::get('/picture-book', [WebViewController::class, 'pictureBook']);
    });
});

Route::group([
    'prefix' => 'app-setting',
], function () {
    Route::group(['middleware'=>['auth:sanctum', 'checkBanned']], function () {
        Route::post('/fcm-token', [AppSettingController::class, 'storageFcmToken']);
    });
});

Route::group([
    'prefix' => 'search',
], function () {
    Route::group(['middleware'=>['auth:sanctum', 'checkBanned']], function () {
        Route::get('/', [SearchAllController::class, 'searchAllByKey']);
        Route::get('/user', [SearchAllController::class, 'searchAllUserByKey']);
        Route::get('/post', [SearchAllController::class, 'searchAllPostByKey']);
    });
});

Route::group([
    'prefix' => 'notifications',
], function () {
    Route::group(['middleware'=>['auth:sanctum', 'checkBanned']], function () {
        Route::get('/', [NotificationController::class, 'getListByUser']);
        Route::get('/status', [NotificationController::class, 'getStatus']);
        Route::post('/official/read/{notificationId}', [NotificationController::class, 'makeOfficialAsRead']);
        Route::post('/mark-as-read', [NotificationController::class, 'markAsReadByType']);
    });
});
