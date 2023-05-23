<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeSomeColumnsInMatchSportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('match_sports', function (Blueprint $table) {
            $table->dropColumn([
                'RATIO_RE_HDP_0',
                'IOR_REH_HDP_0',
                'IOR_REC_HDP_0',
                'RATIO_ROUO_HDP_0',
                'RATIO_ROUU_HDP_0',
                'IOR_ROUH_HDP_0',
                'IOR_ROUC_HDP_0',
            ]); 
            $table->renameColumn("RATIO_RE_HDP_1", "M_LetB_RB_1");
            $table->renameColumn("IOR_REH_HDP_1", "MB_LetB_Rate_RB_1");
            $table->renameColumn("IOR_REC_HDP_1", "TG_LetB_Rate_RB_1");
            $table->renameColumn("RATIO_ROUO_HDP_1", "MB_Dime_RB_1");
            $table->renameColumn("RATIO_ROUU_HDP_1", "TG_Dime_RB_1");
            $table->renameColumn("IOR_ROUC_HDP_1", "MB_Dime_Rate_RB_1");
            $table->renameColumn("IOR_ROUH_HDP_1", "TG_Dime_Rate_RB_1");
            $table->renameColumn("RATIO_RE_HDP_2", "M_LetB_RB_2");
            $table->renameColumn("IOR_REH_HDP_2", "MB_LetB_Rate_RB_2");
            $table->renameColumn("IOR_REC_HDP_2", "TG_LetB_Rate_RB_2");
            $table->renameColumn("RATIO_ROUO_HDP_2", "MB_Dime_RB_2");
            $table->renameColumn("RATIO_ROUU_HDP_2", "TG_Dime_RB_2");
            $table->renameColumn("IOR_ROUH_HDP_2", "MB_Dime_Rate_RB_2");
            $table->renameColumn("IOR_ROUC_HDP_2", "TG_Dime_Rate_RB_2");
            $table->renameColumn("RATIO_RE_HDP_3", "M_LetB_RB_3");
            $table->renameColumn("IOR_REH_HDP_3", "MB_LetB_Rate_RB_3");
            $table->renameColumn("IOR_REC_HDP_3", "TG_LetB_Rate_RB_3");
            $table->renameColumn("RATIO_ROUO_HDP_3", "MB_Dime_RB_3");
            $table->renameColumn("RATIO_ROUU_HDP_3", "TG_Dime_RB_3");
            $table->renameColumn("IOR_ROUH_HDP_3", "MB_Dime_Rate_RB_3");
            $table->renameColumn("IOR_ROUC_HDP_3", "TG_Dime_Rate_RB_3");
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
