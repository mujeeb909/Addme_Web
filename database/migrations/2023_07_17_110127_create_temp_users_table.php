<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTempUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('temp_users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('email');
            $table->string('username')->nullable();
            $table->string('password')->nullable();
            $table->date('dob')->nullable();
            $table->integer('gender')->default(3);
            $table->integer('user_group_id')->nullable();
            $table->string('logo')->nullable();
            $table->string('bio')->nullable();
            $table->integer('provider')->nullable();
            $table->string('device_id')->nullable();
            $table->string('device_type')->nullable();
            $table->string('platform')->nullable(); //ios, android
            $table->string('vcode')->nullable();
            $table->timestamp('vcode_expiry')->nullable();
            $table->string('fcm_token')->nullable();
            $table->integer('allow_data_usage')->nullable();
            $table->timestamp('privacy_policy_date')->nullable();
            $table->timestamp('license_date')->nullable();
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
        Schema::dropIfExists('temp_users');
    }
}
