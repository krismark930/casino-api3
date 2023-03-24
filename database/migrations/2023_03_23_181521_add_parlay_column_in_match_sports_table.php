<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddParlayColumnInMatchSportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('match_sports', function (Blueprint $table) {
            $table->string("MB_P_Points_1")->default("")->nullable();
            $table->string("TG_P_Points_1")->default("")->nullable();
            $table->double("MB_P_Points_Rate_1")->default(0)->nullable();
            $table->double("TG_P_Points_Rate_1")->default(0)->nullable();
            $table->string("MB_P_Points_2")->default("")->nullable();
            $table->string("TG_P_Points_2")->default("")->nullable();
            $table->double("MB_P_Points_Rate_2")->default(0)->nullable();
            $table->double("TG_P_Points_Rate_2")->default(0)->nullable();
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
