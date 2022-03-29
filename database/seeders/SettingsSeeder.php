<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace Database\Seeders;

use App\Enums\Settings\SettingNameEnum;
use App\Enums\Users\RegisterTypeEnum;
use App\Enums\Users\UserStatusEnum;
use App\Models\Setting;
use App\Models\UserSetting;
use App\Traits\HandleExceptionTrait;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsSeeder extends Seeder
{
    use HandleExceptionTrait;
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::beginTransaction();
        try {
            if (!Setting::where('name', SettingNameEnum::ENABLE_NOTIFICATION_POST)->first()) {
                $settingRecord = Setting::create([
                    'name' => SettingNameEnum::ENABLE_NOTIFICATION_POST,
                    'description' => 'Receive notification from post\'s activity: like post, comment on post. The value 1 corresponds to enable',
                    'default_value' => '1',
                ]);
                //insert from user
                $this->insertUserSettingTable($settingRecord);
            }

            if (!Setting::where('name', SettingNameEnum::ENABLE_NOTIFICATION_COMMENT)->first()) {
                $settingRecord = Setting::create([
                    'name' => SettingNameEnum::ENABLE_NOTIFICATION_COMMENT,
                    'description' => 'Receive notification from comment\'s activity: like comment, reply comment. The value 1 corresponds to enable',
                    'default_value' => '1',
                ]);
                //insert from user
                $this->insertUserSettingTable($settingRecord);
            }
            if (!Setting::where('name', SettingNameEnum::ENABLE_NOTIFICATION_FOLLOWING)->first()) {
                $settingRecord = Setting::create([
                    'name' => SettingNameEnum::ENABLE_NOTIFICATION_FOLLOWING,
                    'description' => 'Receive notification from following\'s activity. The value 1 corresponds to enable',
                    'default_value' => '1',
                ]);
                //insert from user
                $this->insertUserSettingTable($settingRecord);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->handleException($e);
        }
    }

    private function insertUserSettingTable($settingRecord)
    {
        DB::table('users')->where(function ($query) {
            $query->where('activated', UserStatusEnum::ACTIVE)
                ->orWhere('register_type', RegisterTypeEnum::REGISTER_NICKNAME);
        })
            ->chunkById(config('settings.chunkDataSize'), function ($users) use ($settingRecord) {
                $userSettings = [];
                foreach ($users as $user) {
                    $userSettings[] = [
                        'user_id' => $user->id,
                        'setting_id' => $settingRecord->id,
                        'value' => $settingRecord->default_value,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];
                }
                UserSetting::insert($userSettings);
            });
    }
}
