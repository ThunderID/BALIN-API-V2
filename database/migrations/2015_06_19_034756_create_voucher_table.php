<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVoucherTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tmp_vouchers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->index()->nullable();
            $table->string('code', 255);
            $table->string('type', 255);
            $table->text('value');
            $table->datetime('started_at')->nullable();
            $table->datetime('expired_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['deleted_at', 'code', 'started_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('tmp_vouchers');
    }
}
