<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserPlaceOwnershipClaimsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_place_ownership_claims', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('requester_user_id');
            $table->unsignedInteger('place_id');
            $table->unsignedInteger('admin_approval_user_id');
            $table->string('admin_comments',175)->nullable();
            $table->boolean('is_approved')->default(false);
            $table->boolean('is_rejected')->default(false);
            $table->timestamp('reviewed_at')->nullable()->default(null);
            //
            $table->timestamps();
            $table->softDeletes();
            //
            $table->foreign('requester_user_id')->references('id')->on('users');
            $table->foreign('admin_approval_user_id')->references('id')->on('users');
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
        Schema::dropIfExists('user_place_ownership_claims');
    }
}
