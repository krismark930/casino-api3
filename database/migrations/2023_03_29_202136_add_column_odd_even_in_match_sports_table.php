<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnOddEvenInMatchSportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('match_sports', function (Blueprint $table) {            
            $table->double("S_Single_Rate_H")->default(0)->nullable();
            $table->double("S_Double_Rate_H")->default(0)->nullable();
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
