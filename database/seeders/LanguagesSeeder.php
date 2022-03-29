<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;

class LanguagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!Language::where('code', 'ja')->first()) {
            Language::create([
                'code' => 'ja',
                'name' => 'Japanese',
            ]);
        }
    }
}
