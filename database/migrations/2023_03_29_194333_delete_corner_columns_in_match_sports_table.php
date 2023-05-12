<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DeleteCornerColumnsInMatchSportsTable extends Migration
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
                'RATIO_ROUO_CN',
                'RATIO_ROUU_CN',
                'IOR_ROUH_CN',
                'IOR_ROUC_CN',
                'RATIO_HROUO_CN',
                'RATIO_HROUU_CN',
                'IOR_HROUH_CN',
                'IOR_HROUC_CN',
                'STR_ODD_CN',
                'STR_EVEN_CN',
                'IOR_REOO_CN',
                'IOR_REOE_CN',
                'STR_HODD_CN',
                'STR_HEVEN_CN',
                'IOR_HREOO_CN',
                'IOR_HREOE_CN',
                'WTYPE_CN',
                'IOR_RNCH_CN',
                'IOR_RNCC_CN'
            ]);
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
