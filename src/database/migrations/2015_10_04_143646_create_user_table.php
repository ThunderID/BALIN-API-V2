<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 255);
            $table->string('email', 255)->unique();
            $table->string('password', 60);
            $table->string('role', 255);
            $table->boolean('is_active');
            $table->string('sso_id', 255);
            $table->string('sso_media', 255);
            $table->text('sso_data');
            $table->enum('gender', ['male', 'female']);
            $table->date('date_of_birth');
            $table->string('activation_link', 255);
            $table->string('reset_password_link', 255);
            $table->datetime('expired_at')->nullable();
            $table->datetime('last_logged_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['deleted_at', 'email']);
            $table->index(['deleted_at', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('users');
    }
}
