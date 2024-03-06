<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
			$table->string('title')->nullable();
			$table->string('menu_url')->nullable();
			$table->integer('parent_id')->nullable();
			$table->string('css_class')->nullable();
			$table->integer('sort')->nullable();
            $table->integer('status')->nullable();
            $table->integer('has_sub_menus')->nullable();
            $table->integer('is_sub_menu')->nullable();
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
        Schema::dropIfExists('menus');
    }
}
