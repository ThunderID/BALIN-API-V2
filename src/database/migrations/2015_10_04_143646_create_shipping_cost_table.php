<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShippingCostTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shipping_costs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('courier_id')->unsigned()->index();
            $table->string('start_postal_code', 6);
            $table->string('end_postal_code', 6);
            $table->double('cost');
            $table->datetime('started_at');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['deleted_at', 'started_at', 'start_postal_code']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('shipping_costs');
    }
}
