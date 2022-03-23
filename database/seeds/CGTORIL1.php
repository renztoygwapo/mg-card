<?php

use Illuminate\Database\Seeder;
use App\CustomerGroup;

class CGTORIL1 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        CustomerGroup::create(['name' => 'TL1-MOTORCYCLE']);
        CustomerGroup::create(['name' => 'TL1-PRIVATE VEHICLE']);
        CustomerGroup::create(['name' => 'TL1-PUV/PUJ']);
        CustomerGroup::create(['name' => 'TL1-TAXI/MULTICAB']);
        CustomerGroup::create(['name' => 'TL1-TRUCK']);
    }
}
