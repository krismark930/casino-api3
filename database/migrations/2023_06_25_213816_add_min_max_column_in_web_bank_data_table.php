<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMinMaxColumnInWebBankDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('web_bank_data', function (Blueprint $table) {
            $table->integer("min_amount")->default(100)->nullable();
            $table->integer("max_amount")->default(10000)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('web_bank_data', function (Blueprint $table) {
            //
        });
    }
}
