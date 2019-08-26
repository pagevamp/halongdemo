<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCruisesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('cruises', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 120);
            $table->string('slug', 120)->unique();
            $table->unsignedBigInteger('route_id')->nullable();
            $table->string('short_description', 450)->comment('short info of about less than 250 characters');
            $table->longText('long_description')->comment('long content, may contain html');
            $table->boolean('published')->default(false)->comment('determines whether it should be diplayed in website');
            $table->string('video')->nullable()->comment('embedded vimeo or youtube video');
            $table->decimal('price', 10, 2)->default(0);
            $table->float('star')->default(0);
            $table->unsignedInteger('featured_index')->nullable()->index('featured_index')->comment('positive integer higher the value more the priority to display first');
            $table->timestamps();

            $table->foreign('route_id')->references('id')->on('routes')->onUpdate('cascade')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return voidphp artisan m
     */
    public function down()
    {
        Schema::dropIfExists('cruises');
    }
}
