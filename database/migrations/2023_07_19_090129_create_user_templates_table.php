<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->integer('user_id');
            $table->string('company_name')->nullable();
            $table->string('company_address')->nullable();
            $table->string('company_logo')->nullable();
            $table->string('profile_image')->nullable();
            $table->string('profile_banner')->nullable();
            $table->string('subtitle')->nullable();
            $table->string('section_color')->nullable();
            $table->string('profile_color')->nullable();
            $table->string('border_color')->nullable();
            $table->string('background_color')->nullable();
            $table->string('button_color')->nullable();
            $table->string('text_color')->nullable();
            $table->string('photo_border_color')->nullable();
            $table->string('background_image')->nullable();
            $table->integer('color_link_icons')->nullable();
            $table->integer('show_contact')->nullable();
            $table->integer('show_connect')->nullable();
            $table->integer('is_editable')->nullable();
            $table->integer('capture_lead')->nullable();
            $table->integer('is_default')->nullable();
            $table->integer('open_direct')->nullable();
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
        Schema::dropIfExists('user_templates');
    }
}
