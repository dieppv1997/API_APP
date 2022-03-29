<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $services = [
            [
                \App\Interfaces\Services\WebView\ApiWebViewServiceInterface::class,
                \App\Services\WebView\ApiWebViewService::class
            ],
            [
                \App\Interfaces\Services\App\AppSettingServiceInterface::class,
                \App\Services\App\AppSettingService::class
            ],
            [
                \App\Interfaces\Services\Comments\CommentServiceInterface::class,
                \App\Services\Comments\CommentService::class
            ],
            [
                \App\Interfaces\Services\Users\AuthenticationServiceInterface::class,
                \App\Services\Users\AuthenticationService::class
            ],
            [
                \App\Interfaces\Services\Users\UserServiceInterface::class,
                \App\Services\Users\UserService::class
            ],
            [
                \App\Interfaces\Services\Users\ResetPasswordServiceInterface::class,
                \App\Services\Users\ResetPasswordService::class
            ],
            [
                \App\Interfaces\Services\Users\ChangeEmailServiceInterface::class,
                \App\Services\Users\ChangeEmailService::class
            ],
            [
                \App\Interfaces\Services\Provinces\ProvinceServiceInterface::class,
                \App\Services\Provinces\ProvinceService::class
            ],
            [
                \App\Interfaces\Services\Users\UserPasswordServiceInterface::class,
                \App\Services\Users\UserPasswordService::class
            ],
            [
                \App\Interfaces\Services\Posts\PostListServiceInterface::class,
                \App\Services\Posts\PostListService::class
            ],
            [
                \App\Interfaces\Services\Posts\PostServiceInterface::class,
                \App\Services\Posts\PostService::class
            ],
            [
                \App\Interfaces\Services\Following\FollowingServiceInterface::class,
                \App\Services\Following\FollowingService::class
            ],
            [
                \App\Interfaces\Services\Tags\TagServiceInterface::class,
                \App\Services\Tag\TagService::class
            ],
            [
                \App\Interfaces\Services\Recommendations\RecommendTagServiceInterface::class,
                \App\Services\Recommendations\RecommendTagService::class
            ],
            [
                \App\Interfaces\Services\Images\ImageHandleServiceInterface::class,
                \App\Services\Images\ImageHandleService::class
            ],
            [
                \App\Interfaces\Services\Search\SearchServiceInterface::class,
                \App\Services\Search\SearchService::class
            ],
            [
                \App\Interfaces\Services\Search\SearchServiceInterface::class,
                \App\Services\Search\SearchService::class
            ],
            [
                \App\Interfaces\Services\Block\BlockServiceInterface::class,
                \App\Services\Block\BlockService::class
            ],
            [
                \App\Interfaces\Services\Users\UserSettingServiceInterface::class,
                \App\Services\Users\UserSettingService::class
            ],
            [
                \App\Interfaces\Services\Notifications\NotificationServiceInterface::class,
                \App\Services\Notifications\NotificationService::class
            ],
        ];
        foreach ($services as $service) {
            $this->app->bind(
                $service[0],
                $service[1]
            );
        }
        if (config('app.env') != 'local') {
            URL::forceScheme('https');
        }
    }
}
