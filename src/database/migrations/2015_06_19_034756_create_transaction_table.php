<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->index();
            $table->integer('supplier_id')->unsigned()->index();
            $table->integer('voucher_id')->unsigned()->index();
            $table->string('ref_number', 255);
            $table->enum('type', ['sell', 'buy']);
            $table->datetime('transact_at');
            $table->integer('unique_number');
            $table->double('shipping_cost');
            $table->double('voucher_discount');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['deleted_at', 'type', 'user_id']);
            $table->index(['deleted_at', 'type', 'transact_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('transactions');
    }
}
