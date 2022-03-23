<?php

namespace App\Console\Commands;

use App\Customer;
use Illuminate\Console\Command;

class ButtonReset extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zero-points';

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
     * @return mixed
     */
    public function handle()
    {
        Customer::query()->update(['points' => 0]);
        echo 'Points Successfully Updated!';
    }
}
