<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePointLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('point_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->index();
            $table->integer('point_log_id')->unsigned()->index();
            $table->integer('reference_id');
            $table->string('reference_type');
            $table->double('amount');
            $table->datetime('expired_at');
            $table->text('notes');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['deleted_at', 'user_id', 'expired_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('point_logs');
    }
}
