<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlaceHoursTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {//0==sunday
        Schema::create('place_hours', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('place_id')->index();
            $table->integer('day_of_week')->index();
            $table->time('open_time');
            $table->time('close_time');
            $table->timestamps();

            $table->unique(['place_id','day_of_week']);
            $table->foreign('place_id')->references('id')->on('places');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('place_hours');
    }
}
