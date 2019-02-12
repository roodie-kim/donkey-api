<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('images', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('post_id')->unsigned()->nullable();
            $table->integer('comment_id')->unsigned()->nullable();
            $table->string('original_name');
            $table->string('new_name');
            $table->string('ext');
            $table->integer('original_width')->unsigned();
            $table->integer('original_height')->unsigned();
            $table->integer('thumb_width')->unsigned();
            $table->integer('thumb_height')->unsigned();
            $table->boolean('uploaded');
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
        Schema::dropIfExists('images');
    }
}
