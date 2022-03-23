<?php

use Illuminate\Database\Seeder;
use App\CustomerGroup;

class CGMATINAAPLAYA extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        CustomerGroup::create(['name' => 'MAP-MOTORCYCLE']);
        CustomerGroup::create(['name' => 'MAP-PRIVATE VEHICLE']);
        CustomerGroup::create(['name' => 'MAP-PUJ/PUV']);
        CustomerGroup::create(['name' => 'MAP-TAXI/MULTICAB']);
        CustomerGroup::create(['name' => 'MAP-TRUCK']);
    }
}
