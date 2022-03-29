<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class IndexUserFollowsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_follows', function (Blueprint $table) {
            $table->index('following_id');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_follows', function (Blueprint $table) {
            $table->dropIndex('following_id');
            $table->dropIndex('user_id');
        });
    }
}
