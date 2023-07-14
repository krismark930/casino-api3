<?php

namespace App\Console\Commands;

use App\Http\Controllers\SportController;
use Illuminate\Console\Command;

class SportsCheckoutCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sports:checkout';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sports Checkout Command';

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
        $sportController = new SportController();
        $sportController->sportsBettingCheckout();
        return 0;
    }
}
