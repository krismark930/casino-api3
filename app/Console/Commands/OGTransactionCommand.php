<?php

namespace App\Console\Commands;

use App\Http\Controllers\Api\User\OGController;
use App\Http\Controllers\ScheduleTestController;
use Illuminate\Console\Command;

class OGTransactionCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'og:transaction';

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
        $OGController = new OGController();
        $OGController->getOGTransaction();

        // $testController = new ScheduleTestController();
        // $testController->scheduleTest();
        return 0;
    }
}
