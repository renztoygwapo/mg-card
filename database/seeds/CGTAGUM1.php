<?php

use App\CustomerGroup;
use Illuminate\Database\Seeder;

class CGTAGUM1 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        CustomerGroup::create(['name' => 'TM1-CONTAINER']);
        CustomerGroup::create(['name' => 'TM1-MOTORCYCLE']);
        CustomerGroup::create(['name' => 'TM1-MULTICAB']);
        CustomerGroup::create(['name' => 'TM1-PRIVATE']);
        CustomerGroup::create(['name' => 'TM1-PUJ']);
        CustomerGroup::create(['name' => 'TM1-TAXI']);
        CustomerGroup::create(['name' => 'TM1-TRUCK']);
        CustomerGroup::create(['name' => 'TM1-VAN']);
    }
}
