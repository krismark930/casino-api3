<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\User\AGController;
use App\Http\Controllers\ScheduleTestController;

class AGTransactionCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ag:transaction';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'AG Transaction Command';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $testController = new ScheduleTestController();
        $testController->scheduleTest();
        $AGController = new AGController();
        $AGController->getAGTransaction();
        $AGController->getEGameTransaction();
        $AGController->getSlotGameTransaction();
        $AGController->getYoplayTransaction();
        $AGController->getHunterTransaction();
        return 0;
    }
}
