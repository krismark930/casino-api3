<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class WebReportTemp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('web_report_temps', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('title');
            $table->string('league');
            $table->string('m_team');
            $table->string('t_team');
            $table->string('select_team');
            $table->string('text');
            $table->string('rate');
            $table->integer('gold');
            $table->string('m_win');
            $table->integer('uid');
            $table->integer('gid');
            $table->integer('line_type');
            $table->string('g_type');
            $table->integer('active');
            $table->timestamps();
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
    }
}
