<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShipmentLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shipment_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('shipment_id')->unsigned()->index();
            $table->string('status', 255);
            $table->datetime('changed_at');
            $table->text('notes');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['deleted_at', 'changed_at', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('shipment_logs');
    }
}
