<?php

/*
 * This file is part of OAuth 2.0 Laravel.
 *
 * (c) Luca Degasperi <packages@lucadegasperi.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * This is the create oauth client table migration class.
 *
 * @author Luca Degasperi <packages@lucadegasperi.com>
 */
class CreateClientTemplateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_template', function (BluePrint $table) {
            $table->increments('id');
            $table->string('client_id', 40);
            $table->string('located');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['id', 'client_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('client_template');
    }
}
