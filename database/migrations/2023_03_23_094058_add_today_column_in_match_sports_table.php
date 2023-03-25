<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTodayColumnInMatchSportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('match_sports', function (Blueprint $table) {
            $table->string("MB_Points_1")->default("")->nullable();
            $table->string("TG_Points_1")->default("")->nullable();
            $table->double("MB_Points_Rate_1")->default(0)->nullable();
            $table->double("TG_Points_Rate_1")->default(0)->nullable();
            $table->string("MB_Points_2")->default("")->nullable();
            $table->string("TG_Points_2")->default("")->nullable();
            $table->double("MB_Points_Rate_2")->default(0)->nullable();
            $table->double("TG_Points_Rate_2")->default(0)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('match_sports', function (Blueprint $table) {
            //
        });
    }
}
