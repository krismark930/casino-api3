<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBkColumnInMatchSportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('match_sports', function (Blueprint $table) {
            $table->string("MB_Points_RB_1")->default("")->nullable();
            $table->string("TG_Points_RB_1")->default("")->nullable();
            $table->double("MB_Points_Rate_RB_1")->default(0)->nullable();
            $table->double("TG_Points_Rate_RB_1")->default(0)->nullable();
            $table->string("MB_Points_RB_2")->default("")->nullable();
            $table->string("TG_Points_RB_2")->default("")->nullable();
            $table->double("MB_Points_Rate_RB_2")->default(0)->nullable();
            $table->double("TG_Points_Rate_RB_2")->default(0)->nullable();
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
