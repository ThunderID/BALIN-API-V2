<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('transaction_id')->unsigned()->index();
            $table->string('method', 255);
            $table->string('destination', 255);
            $table->string('account_name', 255);
            $table->string('account_number', 255);
            $table->date('ondate');
            $table->double('amount');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['deleted_at', 'ondate', 'amount']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('payments');
    }
}
