<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnTodayHDPInMatchSportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('match_sports', function (Blueprint $table) {

            $table->string("M_LetB_1")->default("")->nullable();
            $table->double("MB_LetB_Rate_1")->default(0)->nullable();
            $table->double("TG_LetB_Rate_1")->default(0)->nullable();
            $table->string("MB_Dime_1")->default("")->nullable();
            $table->string("TG_Dime_1")->default("")->nullable();
            $table->double("MB_Dime_Rate_1")->default(0)->nullable();
            $table->double("TG_Dime_Rate_1")->default(0)->nullable();
            $table->string("M_LetB_2")->default("")->nullable();
            $table->double("MB_LetB_Rate_2")->default(0)->nullable();
            $table->double("TG_LetB_Rate_2")->default(0)->nullable();
            $table->string("MB_Dime_2")->default("")->nullable();
            $table->string("TG_Dime_2")->default("")->nullable();
            $table->double("MB_Dime_Rate_2")->default(0)->nullable();
            $table->double("TG_Dime_Rate_2")->default(0)->nullable();
            $table->string("M_LetB_3")->default("")->nullable();
            $table->double("MB_LetB_Rate_3")->default(0)->nullable();
            $table->double("TG_LetB_Rate_3")->default(0)->nullable();
            $table->string("MB_Dime_3")->default("")->nullable();
            $table->string("TG_Dime_3")->default("")->nullable();
            $table->double("MB_Dime_Rate_3")->default(0)->nullable();
            $table->double("TG_Dime_Rate_3")->default(0)->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('matach_sports', function (Blueprint $table) {
            //
        });
    }
}
