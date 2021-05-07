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
            $table->string('image_url')->nullable();
            $table->string('summary')->nullable();
            $table->text('description')->nullable();
            $table->unsignedInteger('price')->default(1);
            $table->string('address')->nullable();//nullable because food trucks may not have a set address
            $table->string('city')->default('Wichita');
            $table->char('state_code',2)->default('KS');
            $table->unsignedInteger('owner_user_id')->nullable();
            //contact info
            $table->string('phone_number')->nullable();
            $table->string('email_address')->nullable();
            $table->string('menu_link')->nullable();
            $table->string('website_url')->nullable();
            $table->string('facebook_link')->nullable();
            $table->string('instagram_link')->nullable();
            //
            //extra special attributes
            $table->boolean('has_vegan_options')->default(false);
            $table->boolean('has_gluten_free_options')->default(false);
            $table->boolean('has_bike_rack')->default(false);
            $table->boolean('has_ev_charger')->default(false);
            $table->boolean('has_carryout')->default(false);
            $table->boolean('has_public_wifi')->default(false);
            $table->boolean('has_delivery')->default(false);
            $table->boolean('serves_alcohol')->default(false);
            $table->boolean('serves_full_meals')->default(false);
            $table->boolean('serves_brunch')->default(false);
            $table->boolean('is_food_truck')->default(false);

            //location info
            $table->decimal('latitude',10,8)->nullable()->index();
            $table->decimal('longitude',11,8)->nullable()->index();
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
        Schema::dropIfExists('places');
    }
}
