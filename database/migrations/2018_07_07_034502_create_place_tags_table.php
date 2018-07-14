<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlaceTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('place_tags', function (Blueprint $table) {
            $table->unsignedInteger('place_id')->index();
            $table->unsignedInteger('tag_id')->index();
            $table->timestamps();
            $table->unique('place_id','tag_id');
            $table->foreign('tag_id')->references('id')->on('tags');
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
        Schema::dropIfExists('place_tags');
    }
}
