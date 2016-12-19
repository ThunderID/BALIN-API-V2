<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStoreSettingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tmp_store_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type', 255);
            $table->text('value');
            $table->datetime('started_at');
            $table->datetime('ended_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['deleted_at', 'type', 'started_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('tmp_store_settings');
    }
}
