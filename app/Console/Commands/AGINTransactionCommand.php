<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Api\User\AGController;

class AGINTransactionCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'agin:transaction';

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
        $AGController = new AGController();
        $AGController->getFTPAGINTransaction();
        return 0;
    }
}
