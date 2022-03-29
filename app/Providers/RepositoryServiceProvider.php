<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Interfaces\Repositories;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $services = [
            [
                \App\Interfaces\Repositories\ProvinceRepositoryInterface::class,
                \App\Repositories\ProvinceRepositoryEloquent::class
            ],
            [
                \App\Interfaces\Repositories\UserRepositoryInterface::class,
                \App\Repositories\UserRepositoryEloquent::class
            ],
            [
                \App\Interfaces\Repositories\VerifyUserRepositoryInterface::class,
                \App\Repositories\VerifyUserRepositoryEloquent::class
            ],
            [
                \App\Interfaces\Repositories\PasswordResetRepositoryInterface::class,
                \App\Repositories\PasswordResetRepositoryEloquent::class
            ],
            [
                \App\Interfaces\Repositories\CommentRepositoryInterface::class,
                \App\Repositories\CommentRepositoryEloquent::class
            ],
            [
                \App\Interfaces\Repositories\CommentLikeRepositoryInterface::class,
                \App\Repositories\CommentLikeRepositoryEloquent::class
            ],
            [
                \App\Interfaces\Repositories\PlaceRepositoryInterface::class,
                \App\Repositories\PlaceRepositoryEloquent::class
            ],
            [
                \App\Interfaces\Repositories\PostRepositoryInterface::class,
                \App\Repositories\PostRepositoryEloquent::class
            ],
            [
                \App\Interfaces\Repositories\TagRepositoryInterface::class,
                \App\Repositories\TagRepositoryEloquent::class
            ],
            [
                \App\Interfaces\Repositories\PostTagRepositoryInterface::class,
                \App\Repositories\PostTagRepositoryEloquent::class
            ],
            [
                \App\Interfaces\Repositories\UserFollowRepositoryInterface::class,
                \App\Repositories\UserFollowRepositoryEloquent::class
            ],
            [
                \App\Interfaces\Repositories\UserSettingRepositoryInterface::class,
                \App\Repositories\UserSettingRepositoryEloquent::class
            ],
            [
                \App\Interfaces\Repositories\PostLikeRepositoryInterface::class,
                \App\Repositories\PostLikeRepositoryEloquent::class
            ],
            [
                \App\Interfaces\Repositories\PostPublishedSequenceRepositoryInterface::class,
                \App\Repositories\PostPublishedSequenceRepositoryEloquent::class
            ],
            [
                \App\Interfaces\Repositories\ChangeEmailRequestRepositoryInterface::class,
                \App\Repositories\ChangeEmailRequestRepositoryEloquent::class
            ],
            [
                \App\Interfaces\Repositories\FCMTokenRepositoryInterface::class,
                \App\Repositories\FCMTokenRepositoryEloquent::class
            ],
            [
                \App\Interfaces\Repositories\NotificationTemplateRepositoryInterface::class,
                \App\Repositories\NotificationTemplateRepositoryEloquent::class
            ],
            [
                \App\Interfaces\Repositories\NotificationTemplateTranslationRepositoryInterface::class,
                \App\Repositories\NotificationTemplateTranslationRepositoryEloquent::class
            ],
            [
                \App\Interfaces\Repositories\NotificationRepositoryInterface::class,
                \App\Repositories\NotificationRepositoryEloquent::class
            ],
            [
                \App\Interfaces\Repositories\OfficialNotificationRepositoryInterface::class,
                \App\Repositories\OfficialNotificationRepositoryEloquent::class
            ],
            [
                \App\Interfaces\Repositories\UserBlockRepositoryInterface::class,
                \App\Repositories\UserBlockRepositoryEloquent::class
            ],
            [
                \App\Interfaces\Repositories\SettingRepositoryInterface::class,
                \App\Repositories\SettingRepositoryEloquent::class
            ],
            [
                \App\Interfaces\Repositories\BadWordRepositoryInterface::class,
                \App\Repositories\BadWordRepositoryEloquent::class
            ],
        ];
        foreach ($services as $service) {
            $this->app->bind(
                $service[0],
                $service[1]
            );
        }
        //:end-bindings:
    }
}
