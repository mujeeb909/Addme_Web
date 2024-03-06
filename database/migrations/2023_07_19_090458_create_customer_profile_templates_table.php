<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerProfileTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_profile_templates', function (Blueprint $table) {
            $table->id();
            $table->integer('user_template_id');
            $table->string('profile_link');
            $table->string('profile_code');
            $table->string('title')->nullable();
            $table->string('file_image')->nullable();
            $table->string('icon')->nullable();
            $table->integer('user_id')->default(0);
            $table->integer('is_business')->default(0);
            $table->integer('status')->default(1);
            $table->integer('is_direct')->default(0);
            $table->integer('sequence')->default(0);
            $table->integer('is_focused')->nullable();
            $table->integer('is_default')->nullable();
            $table->integer('global_id')->nullable();
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
        Schema::dropIfExists('customer_profile_templates');
    }
}
