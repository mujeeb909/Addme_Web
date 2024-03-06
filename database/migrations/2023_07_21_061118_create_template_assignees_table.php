<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTemplateAssigneesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('template_assignees', function (Blueprint $table) {
            $table->id();
            $table->integer('user_template_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->integer('customer_profile_id')->nullable();
            $table->integer('customer_profile_template_id')->nullable();
            $table->integer('is_assigned')->default(1);
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
        Schema::dropIfExists('template_assignees');
    }
}
