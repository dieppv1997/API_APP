<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace Database\Seeders;

use App\Models\Province;
use App\Models\ProvinceTranslation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use function PHPUnit\Framework\throwException;

class ProvinceTranslationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::beginTransaction();
        try {
            ProvinceTranslation::truncate();
            Province::truncate();
            for ($i = 1; $i < 48; $i++){
                $provinces = new Province();
                $provinces->id= $i;
                $provinces->save();
            }
            $provinces = [
                ['province_id' => '1', 'lang_code' => 'ja', 'name' => '北海道'],
                ['province_id' => '2', 'lang_code' => 'ja', 'name' => '青森県'],
                ['province_id' => '3', 'lang_code' => 'ja', 'name' => '岩手県'],
                ['province_id' => '4', 'lang_code' => 'ja', 'name' => '宮城県'],
                ['province_id' => '5', 'lang_code' => 'ja', 'name' => '秋田県'],
                ['province_id' => '6', 'lang_code' => 'ja', 'name' => '山形県'],
                ['province_id' => '7', 'lang_code' => 'ja', 'name' => '福島県'],
                ['province_id' => '8', 'lang_code' => 'ja', 'name' => '茨城県'],
                ['province_id' => '9', 'lang_code' => 'ja', 'name' => '栃木県'],
                ['province_id' => '10', 'lang_code' => 'ja', 'name' => '群馬県'],
                ['province_id' => '11', 'lang_code' => 'ja', 'name' => '埼玉県'],
                ['province_id' => '12', 'lang_code' => 'ja', 'name' => '千葉県'],
                ['province_id' => '13', 'lang_code' => 'ja', 'name' => '東京都'],
                ['province_id' => '14', 'lang_code' => 'ja', 'name' => '神奈川県'],
                ['province_id' => '15', 'lang_code' => 'ja', 'name' => '新潟県'],
                ['province_id' => '16', 'lang_code' => 'ja', 'name' => '富山県'],
                ['province_id' => '17', 'lang_code' => 'ja', 'name' => '石川県'],
                ['province_id' => '18', 'lang_code' => 'ja', 'name' => '福井県'],
                ['province_id' => '19', 'lang_code' => 'ja', 'name' => '山梨県'],
                ['province_id' => '20', 'lang_code' => 'ja', 'name' => '長野県'],
                ['province_id' => '21', 'lang_code' => 'ja', 'name' => '岐阜県'],
                ['province_id' => '22', 'lang_code' => 'ja', 'name' => '静岡県'],
                ['province_id' => '23', 'lang_code' => 'ja', 'name' => '愛知県'],
                ['province_id' => '24', 'lang_code' => 'ja', 'name' => '三重県'],
                ['province_id' => '25', 'lang_code' => 'ja', 'name' => '滋賀県'],
                ['province_id' => '26', 'lang_code' => 'ja', 'name' => '京都府'],
                ['province_id' => '27', 'lang_code' => 'ja', 'name' => '大阪府'],
                ['province_id' => '28', 'lang_code' => 'ja', 'name' => '兵庫県'],
                ['province_id' => '29', 'lang_code' => 'ja', 'name' => '奈良県'],
                ['province_id' => '30', 'lang_code' => 'ja', 'name' => '和歌山県'],
                ['province_id' => '31', 'lang_code' => 'ja', 'name' => '鳥取県'],
                ['province_id' => '32', 'lang_code' => 'ja', 'name' => '島根県'],
                ['province_id' => '33', 'lang_code' => 'ja', 'name' => '岡山県'],
                ['province_id' => '34', 'lang_code' => 'ja', 'name' => '広島県'],
                ['province_id' => '35', 'lang_code' => 'ja', 'name' => '山口県'],
                ['province_id' => '36', 'lang_code' => 'ja', 'name' => '徳島県'],
                ['province_id' => '37', 'lang_code' => 'ja', 'name' => '香川県'],
                ['province_id' => '38', 'lang_code' => 'ja', 'name' => '愛媛県'],
                ['province_id' => '39', 'lang_code' => 'ja', 'name' => '高知県'],
                ['province_id' => '40', 'lang_code' => 'ja', 'name' => '福岡県'],
                ['province_id' => '41', 'lang_code' => 'ja', 'name' => '佐賀県'],
                ['province_id' => '42', 'lang_code' => 'ja', 'name' => '長崎県'],
                ['province_id' => '43', 'lang_code' => 'ja', 'name' => '熊本県'],
                ['province_id' => '44', 'lang_code' => 'ja', 'name' => '大分県'],
                ['province_id' => '45', 'lang_code' => 'ja', 'name' => '宮崎県'],
                ['province_id' => '46', 'lang_code' => 'ja', 'name' => '鹿児島県'],
                ['province_id' => '47', 'lang_code' => 'ja', 'name' => '沖縄県'],
            ];
            foreach ($provinces as $key => $value) {
                ProvinceTranslation::create($value);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            throw new Exception($exception->getMessage());
        }
    }
}
