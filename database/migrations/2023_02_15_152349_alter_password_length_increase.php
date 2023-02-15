<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterPasswordLengthIncrease extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('web_system_data', function (Blueprint $table) {
            $table->string('PassWord', 255)->change();
        });
        Schema::table('web_member_data', function (Blueprint $table) {
            $table->string('PassWord', 255)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('web_system_data', function (Blueprint $table) {
            $table->string('PassWord', 15)->change();
        });
        Schema::table('web_member_data', function (Blueprint $table) {
            $table->string('PassWord', 15)->change();
        });
    }
}
