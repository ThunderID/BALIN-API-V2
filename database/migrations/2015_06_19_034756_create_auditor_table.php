<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAuditorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('auditors', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->index();
            $table->integer('table_id')->unsigned()->index();
            $table->string('table_type', 255);
            $table->datetime('ondate');
            $table->string('event', 255);
            $table->string('type', 255);
            $table->text('action');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['deleted_at', 'type', 'ondate']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('auditors');
    }
}
