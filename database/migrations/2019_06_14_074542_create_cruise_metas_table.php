<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCruiseMetasTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('cruise_metas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('cruise_id')->index();
            $table->foreign('cruise_id')->references('id')->on('cruises')->onUpdate('cascade')->onDelete('cascade');
            $table->string('name', 120)->index();
            $table->longText('value');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('cruise_metas');
    }
}
