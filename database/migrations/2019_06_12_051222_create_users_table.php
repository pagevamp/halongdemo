<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 150)->index()->comment('full capitalized name');
            $table->string('email', 150)->index()->unique();
            $table->string('username', 150)->index()->unique()->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->text('avatar', 250)->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('suspended_at')->nullable();
            $table->string('password', 150)->nullable();
            $table->string('api_token', 80)->unique()->nullable()->default(null)->comment('hashed token for authentication');
            $table->tinyInteger('role')->defaule(2)->comment('user role; 1 => admin; 2 => agent; 3 => client');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
