<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToWebMemberDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('web_member_data', function (Blueprint $table) {
            $table->float("bonus_amount", 15, 2)->nullable();
            $table->float("deposit_amount", 15, 2)->nullable();
            $table->float("consumption_amount", 15, 2)->nullable();
            $table->float("withdrawal_condition", 15, 2)->nullable();
            $table->float("condition_multiplier", 15, 2)->default(1.00)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('web_member_data', function (Blueprint $table) {
            //
        });
    }
}
