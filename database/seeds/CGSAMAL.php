<?php

use Illuminate\Database\Seeder;
use App\CustomerGroup;

class CGSAMAL extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        CustomerGroup::create(['name' => 'SML-CONTAINER']);
        CustomerGroup::create(['name' => 'SML-MOTORCYCLE']);
        CustomerGroup::create(['name' => 'SML-MULTICAB/TAXI']);
        CustomerGroup::create(['name' => 'SML-PRIVATE VEHICLE']);
        CustomerGroup::create(['name' => 'SML-TRUCK']);
    }
}
