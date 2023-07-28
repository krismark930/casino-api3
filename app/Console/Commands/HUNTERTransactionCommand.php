<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Api\User\AGController;
use App\Http\Controllers\ScheduleTestController;

class HUNTERTransactionCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hunter:transaction';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $AGController = new AGController();
        $AGController->getHunterTransaction();

        // $testController = new ScheduleTestController();
        // $testController->scheduleTest();
        return 0;
    }
}
