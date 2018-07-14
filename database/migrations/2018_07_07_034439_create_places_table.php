<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlacesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('places', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('image')->nullable();
            $table->text('description')->nullable();
            $table->string('address')->nullable();//nullable because food trucks may not have a set address
            $table->string('city')->default('Wichita');
            $table->char('state_code',2)->default('KS');
            //
            //contact info
            $table->string('phone_number')->nullable();
            $table->string('email_address')->nullable();
            //
            //extra special attributes
            $table->boolean('has_vegan_options')->default(false);
            $table->boolean('has_gluten_free_options')->default(false);
            $table->boolean('is_food_truck')->default(false);
            //location info
            //
            $table->string('google_place_id')->nullable();
            $table->decimal('latitude',10,8)->nullable()->index();
            $table->decimal('longitude',11,8)->nullable()->index();
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
        Schema::dropIfExists('places');
    }
}
