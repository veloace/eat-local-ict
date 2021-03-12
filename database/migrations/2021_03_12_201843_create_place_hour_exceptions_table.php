<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlaceHourExceptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('place_hour_exceptions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('place_id');
            $table->unsignedSmallInteger('day_of_week')->index();
            $table->time('start')->nullable();//only nullable if either open_all_day or closed is trued
            $table->time('end')->nullable();//only nullable if either open_all_day or closed is trued
            $table->boolean('open_all_day')->default(false);//if the place is open from midnight to midnight
            $table->boolean('closed')->default(false);//if the place is not open at all
            $table->string('explanation')->nullable();//any applicable explanation, such as "Closed for Christmas"
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
        Schema::dropIfExists('place_hour_exceptions');
    }
}
