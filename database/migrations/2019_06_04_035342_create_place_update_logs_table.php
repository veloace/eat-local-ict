<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlaceUpdateLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('place_update_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('place_id');
            $table->unsignedInteger('edited_by_user_id');
            $table->text('request_json');
            $table->text('new_model_json');
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
        Schema::dropIfExists('place_update_logs');
    }
}
