<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChangeEmailRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('change_email_requests', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->unique();
            $table->string('verify_code', 255);
            $table->string('email', 255);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('change_email_requests');
    }
}
