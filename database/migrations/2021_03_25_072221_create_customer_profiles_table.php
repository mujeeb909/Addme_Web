<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('profile_link');
            $table->string('profile_code');
            $table->integer('user_id');
            $table->string('title')->nullable();
            $table->string('file_image')->nullable();
            $table->integer('is_business')->default(0);
            $table->integer('status')->default(1);
            $table->integer('is_direct')->default(0);
            $table->string('icon')->nullable();
            $table->integer('sequence')->default(0);
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
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
        Schema::dropIfExists('customer_profiles');
    }
}
