<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('email')->unique();
            $table->string('username')->nullable();
            $table->string('password')->nullable();
            $table->date('dob')->nullable();
            $table->integer('gender')->default(3);
            $table->integer('user_group_id')->nullable();
            $table->string('logo')->nullable();
            $table->string('bio')->nullable();
            $table->integer('is_pro')->default(0);
            $table->integer('is_public')->default(1);
            $table->string('profile_view')->default('personal');
            $table->integer('status')->default(1);
            $table->integer('provider')->nullable();
            $table->string('device_id')->nullable();
            $table->string('device_type')->nullable();
            $table->string('platform')->nullable(); //ios, android
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('last_login')->nullable();
            $table->string('vcode')->nullable();
            $table->timestamp('vcode_expiry')->nullable();
            $table->timestamp('subscription_date')->nullable();
            $table->timestamp('subscription_expires_on')->nullable();
            $table->integer('open_direct')->default(0);
            $table->string('fcm_token')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
