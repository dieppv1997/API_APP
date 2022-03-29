<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnIntoPostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->integer('trees_id')->after('place_id')->nullable();
            $table->integer('genre_id')->after('place_id')->nullable();
            $table->integer('taste_id')->after('place_id')->nullable();
            $table->integer('plan_id')->after('place_id')->nullable();
            $table->integer('main_color_id')->after('place_id')->nullable();
            $table->integer('price_range_id')->after('place_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn('trees_id');
            $table->dropColumn('genre_id');
            $table->dropColumn('taste_id');
            $table->dropColumn('plan_id');
            $table->dropColumn('main_color_id');
            $table->dropColumn('price_range_id');
        });
    }
}
