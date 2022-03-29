<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace Database\Seeders;

use App\Enums\Notifications\NotificationTemplateNameEnum;
use App\Enums\Settings\SettingNameEnum;
use App\Models\NotificationTemplate;
use App\Models\NotificationTemplateTranslation;
use App\Models\Setting;
use App\Traits\HandleExceptionTrait;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Exception;

class NotificationTemplateSeeder extends Seeder
{
    use HandleExceptionTrait;
    /**
     * Run the database seeds.
     * @throws Exception
     * @return void
     */
    public function run()
    {
        DB::beginTransaction();
        try {
            NotificationTemplate::truncate();
            NotificationTemplateTranslation::truncate();
            $japaneseLangCode = 'ja';
            $settings = Setting::all()->pluck('id', 'name');

            //Notification template like post
            $likePostNotificationTemplate = NotificationTemplate::updateOrCreate([
                'description' => 'Notification when user like my post',
                'name' => NotificationTemplateNameEnum::LIKE_POST,
                'entity_type' => 'App\Models\Post',
                'setting_id' => isset($settings[SettingNameEnum::ENABLE_NOTIFICATION_POST]) ?
                    $settings[SettingNameEnum::ENABLE_NOTIFICATION_POST] : null
            ]);
            NotificationTemplateTranslation::updateOrCreate([
                'notification_template_id' => $likePostNotificationTemplate->id,
                'lang_code' => $japaneseLangCode,
                'title' => 'Like post',
                'body' => '{username}さんがあなたの投稿に「いいね」しました。',
            ]);

            //Notification template like comment
            $likeCommentNotificationTemplate = NotificationTemplate::updateOrCreate([
                'description' => 'Notification when user like my comment',
                'name' => NotificationTemplateNameEnum::LIKE_COMMENT,
                'entity_type' => 'App\Models\Comment',
                'setting_id' => isset($settings[SettingNameEnum::ENABLE_NOTIFICATION_COMMENT]) ?
                    $settings[SettingNameEnum::ENABLE_NOTIFICATION_COMMENT] : null
            ]);
            NotificationTemplateTranslation::updateOrCreate([
                'notification_template_id' => $likeCommentNotificationTemplate->id,
                'lang_code' => $japaneseLangCode,
                'title' => 'Like comment',
                'body' => '{username}さんがあなたのコメントに「いいね」しました。',
            ]);

            //Notification template new comment
            $newCommentNotificationTemplate = NotificationTemplate::updateOrCreate([
                'description' => 'Notification when someone post new comment on my post',
                'name' => NotificationTemplateNameEnum::NEW_COMMENT,
                'entity_type' => 'App\Models\Comment',
                'setting_id' => isset($settings[SettingNameEnum::ENABLE_NOTIFICATION_POST]) ?
                    $settings[SettingNameEnum::ENABLE_NOTIFICATION_POST] : null
            ]);
            NotificationTemplateTranslation::updateOrCreate([
                'notification_template_id' => $newCommentNotificationTemplate->id,
                'lang_code' => $japaneseLangCode,
                'title' => 'New comment',
                'body' => '{username}さんがあなたの投稿にコメントしました。',
            ]);

            //Notification template follow user public
            $newPublicFollowNotificationTemplate = NotificationTemplate::updateOrCreate([
                'description' => 'Notification someone follow me when i\'m public ',
                'name' => NotificationTemplateNameEnum::FOLLOW_USER_PUBLIC,
                'entity_type' => 'App\Models\UserFollow',
                'setting_id' => isset($settings[SettingNameEnum::ENABLE_NOTIFICATION_FOLLOWING]) ?
                    $settings[SettingNameEnum::ENABLE_NOTIFICATION_FOLLOWING] : null
            ]);
            NotificationTemplateTranslation::updateOrCreate([
                'notification_template_id' => $newPublicFollowNotificationTemplate->id,
                'lang_code' => $japaneseLangCode,
                'title' => 'New follow',
                'body' => '{username}さんがあなたをフォローしました。',
            ]);

            //Notification template follow user private
            $newPrivateFollowNotificationTemplate = NotificationTemplate::updateOrCreate([
                'description' => 'Notification someone send request follow to me when i\'m private',
                'name' => NotificationTemplateNameEnum::FOLLOW_USER_PRIVATE,
                'entity_type' => 'App\Models\UserFollow',
                'setting_id' => isset($settings[SettingNameEnum::ENABLE_NOTIFICATION_FOLLOWING]) ?
                    $settings[SettingNameEnum::ENABLE_NOTIFICATION_FOLLOWING] : null
            ]);
            NotificationTemplateTranslation::updateOrCreate([
                'notification_template_id' => $newPrivateFollowNotificationTemplate->id,
                'lang_code' => $japaneseLangCode,
                'title' => 'New follow private',
                'body' => '{username}さんからのフォローリクエスト。',
            ]);

            //Notification template when someone reply my comment
            $newPrivateFollowNotificationTemplate = NotificationTemplate::updateOrCreate([
                'description' => 'Notification when someone reply my comment',
                'name' => NotificationTemplateNameEnum::NEW_REPLY_COMMENT,
                'entity_type' => 'App\Models\Comment',
                'setting_id' => isset($settings[SettingNameEnum::ENABLE_NOTIFICATION_COMMENT]) ?
                    $settings[SettingNameEnum::ENABLE_NOTIFICATION_COMMENT] : null
            ]);
            NotificationTemplateTranslation::updateOrCreate([
                'notification_template_id' => $newPrivateFollowNotificationTemplate->id,
                'lang_code' => $japaneseLangCode,
                'title' => 'New reply comment',
                'body' => '{username}さんがあなたのコメントに返信しました。',
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            $this->handleException($e);
        }
    }
}
