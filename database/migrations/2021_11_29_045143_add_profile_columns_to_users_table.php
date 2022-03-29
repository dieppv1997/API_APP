<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProfileColumnsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nickname', 50)->change();
            $table->string('avatar_image', 255)->after('password')->nullable();
            $table->string('cover_image', 255)->after('avatar_image')->nullable();
            $table->date('birthday')->after('cover_image')->nullable();
            $table->tinyInteger('gender')->after('birthday')->nullable();
            $table->string('favorite_shop', 50)->after('gender')->nullable();
            $table->string('favorite_place', 50)->after('favorite_shop')->nullable();
            $table->string('intro',150)->after('favorite_place')->nullable();
            $table->integer('province_id')->after('intro')->nullable();
            $table->tinyInteger('status')->after('province_id')->default(0);
            $table->string('device_id', 100)->after('status')->nullable();
            $table->string('email')->nullable()->change();
            $table->string('password')->nullable()->change();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nickname', 255)->change();
            $table->dropColumn('avatar_image');
            $table->dropColumn('cover_image');
            $table->dropColumn('birthday');
            $table->dropColumn('gender');
            $table->dropColumn('favorite_shop');
            $table->dropColumn('favorite_place');
            $table->dropColumn('intro');
            $table->dropColumn('province_id');
            $table->dropColumn('status');
            $table->dropColumn('device_id');
            $table->string('email')->nullable(false)->change();
            $table->string('password')->nullable(false)->change();
        });
    }
}
