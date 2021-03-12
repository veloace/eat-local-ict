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
    {
        Schema::create('place_hours', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('place_id');
            $table->unsignedSmallInteger('day_of_week')->index();
            $table->time('start')->nullable();//only nullable if either open_all_day or closed is trued
            $table->time('end')->nullable();//only nullable if either open_all_day or closed is trued
            $table->boolean('open_all_day')->default(false);//if the place is open from midnight to midnight
            $table->boolean('closed')->default(false);//if the place is not open at all
            $table->boolean('spans_to_next_day')->default(false);//if the closing time is the next day (such as a bar that closes at 2AM)
            $table->string('explanation')->nullable();//any applicable explanation, such as closed at lunch
            $table->timestamps();

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
