<?php

use Illuminate\Database\Seeder;
use App\CustomerGroup;

class CGTUGBOK extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {CustomerGroup::create(['name' => 'TBK-MOTORCYCLE']);
        CustomerGroup::create(['name' => 'TBK-MULTICAB/TAXI']);
        CustomerGroup::create(['name' => 'TBK-PRIVATE VEHICLE']);
        CustomerGroup::create(['name' => 'TBK-PUV/PUJ']);
        CustomerGroup::create(['name' => 'TBK-TRUCK']);
        
    }
}
