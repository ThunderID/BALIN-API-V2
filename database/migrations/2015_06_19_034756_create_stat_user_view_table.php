<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStatUserViewTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stat_user_views', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->index();
            $table->integer('statable_id')->unsigned()->index();
            $table->string('statable_type', 255);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['deleted_at', 'statable_id', 'created_at']);
            $table->index(['deleted_at', 'user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('stat_user_views');
    }
}
