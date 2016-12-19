<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserCampaignTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_campaign', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->index();
            $table->integer('voucher_id')->unsigned()->index();
            $table->string('type', 255);
            $table->boolean('is_used');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['deleted_at', 'user_id', 'type']);
            $table->index(['deleted_at', 'is_used']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('user_campaign');
    }
}
