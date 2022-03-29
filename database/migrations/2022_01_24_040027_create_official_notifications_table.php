<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOfficialNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('official_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('title', 30);
            $table->string('web_link', 255);
            $table->date('start_show_date');
            $table->date('end_show_date');
            $table->timestamps();
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
        Schema::dropIfExists('official_notifications');
    }
}
