<?php

namespace App\Console\Commands;

use App\Http\Controllers\Api\User\OGController;
use Illuminate\Console\Command;

class OGTokenCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'og:token';

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
        $OGController->getOGToken();
        return 0;
    }
}
