<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMainColorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('main_colors', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 255);
            $table->string('code', 255);
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
        Schema::dropIfExists('main_colors');
    }
}
