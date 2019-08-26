<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCruiseTotalAverageRatingsColumn extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('cruises', function (Blueprint $table) {
            $table->decimal('total_average_rating', 2, 1)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('cruises', function (Blueprint $table) {
            $table->dropIfExists('total_average_rating');
        });
    }
}
