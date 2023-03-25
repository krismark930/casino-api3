<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHdpCornerBookingInMatchSportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('match_sports', function (Blueprint $table) {
            $table->boolean("HDP_OU")->default(false)->nullable();
            $table->boolean("CORNER")->default(false)->nullable();
            $table->boolean("BOOKING")->default(false)->nullable();
            $table->double("RATIO_RE_HDP_0")->default(0)->nullable();
            $table->double("IOR_REH_HDP_0")->default(0)->nullable();
            $table->double("IOR_REC_HDP_0")->default(0)->nullable();
            $table->string("RATIO_ROUO_HDP_0")->default("")->nullable();
            $table->string("RATIO_ROUU_HDP_0")->default("")->nullable();
            $table->double("IOR_ROUH_HDP_0")->default(0)->nullable();
            $table->double("IOR_ROUC_HDP_0")->default(0)->nullable();
            $table->string("RATIO_RE_HDP_1")->default("")->nullable();
            $table->double("IOR_REH_HDP_1")->default(0)->nullable();
            $table->double("IOR_REC_HDP_1")->default(0)->nullable();
            $table->string("RATIO_ROUO_HDP_1")->default("")->nullable();
            $table->string("RATIO_ROUU_HDP_1")->default("")->nullable();
            $table->double("IOR_ROUC_HDP_1")->default(0)->nullable();
            $table->double("IOR_ROUH_HDP_1")->default(0)->nullable();
            $table->string("RATIO_RE_HDP_2")->default("")->nullable();
            $table->double("IOR_REH_HDP_2")->default(0)->nullable();
            $table->double("IOR_REC_HDP_2")->default(0)->nullable();
            $table->string("RATIO_ROUO_HDP_2")->default("")->nullable();
            $table->string("RATIO_ROUU_HDP_2")->default("")->nullable();
            $table->double("IOR_ROUH_HDP_2")->default(0)->nullable();
            $table->double("IOR_ROUC_HDP_2")->default(0)->nullable();
            $table->string("RATIO_RE_HDP_3")->default("")->nullable();
            $table->double("IOR_REH_HDP_3")->default(0)->nullable();
            $table->double("IOR_REC_HDP_3")->default(0)->nullable();
            $table->string("RATIO_ROUO_HDP_3")->default("")->nullable();
            $table->string("RATIO_ROUU_HDP_3")->default("")->nullable();
            $table->double("IOR_ROUH_HDP_3")->default(0)->nullable();
            $table->double("IOR_ROUC_HDP_3")->default(0)->nullable();
            $table->string("RATIO_ROUO_CN")->default("")->nullable();
            $table->string("RATIO_ROUU_CN")->default("")->nullable();
            $table->double("IOR_ROUH_CN")->default(0)->nullable();
            $table->double("IOR_ROUC_CN")->default(0)->nullable();
            $table->string("RATIO_HROUO_CN")->default("")->nullable();
            $table->string("RATIO_HROUU_CN")->default("")->nullable();
            $table->double("IOR_HROUH_CN")->default(0)->nullable();
            $table->double("IOR_HROUC_CN")->default(0)->nullable();
            $table->string("STR_ODD_CN")->default("")->nullable();
            $table->string("STR_EVEN_CN")->default("")->nullable();
            $table->double("IOR_REOO_CN")->default(0)->nullable();
            $table->double("IOR_REOE_CN")->default(0)->nullable();
            $table->string("STR_HODD_CN")->default("")->nullable();
            $table->string("STR_HEVEN_CN")->default("")->nullable();
            $table->double("IOR_HREOO_CN")->default(0)->nullable();
            $table->double("IOR_HREOE_CN")->default(0)->nullable();
            $table->string("WTYPE_CN")->default("")->nullable();
            $table->double("IOR_RNCH_CN")->default(0)->nullable();
            $table->double("IOR_RNCC_CN")->default(0)->nullable();
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
