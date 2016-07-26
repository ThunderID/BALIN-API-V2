<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImageTable extends Migration
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
            $table->integer('imageable_id')->unsigned()->index();
            $table->string('imageable_type', 255);
            $table->string('thumbnail', 255);
            $table->string('image_xs', 255);
            $table->string('image_sm', 255);
            $table->string('image_md', 255);
            $table->string('image_lg', 255);
            $table->boolean('is_default');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['deleted_at', 'imageable_id', 'imageable_type', 'is_default']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('images');
    }
}
