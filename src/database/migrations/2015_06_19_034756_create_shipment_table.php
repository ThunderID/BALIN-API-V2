<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShipmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('courier_id')->unsigned()->index();
            $table->integer('transaction_id')->unsigned()->index();
            $table->integer('address_id')->unsigned()->index();
            $table->string('receipt_number', 255)->nullable();
            $table->string('receiver_name', 255);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['deleted_at', 'transaction_id', 'address_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('shipments');
    }
}
