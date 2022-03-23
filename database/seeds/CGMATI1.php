<?php

use Illuminate\Database\Seeder;
use App\CustomerGroup;

class CGMATI1 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        CustomerGroup::create(['name' => 'MATI1-LIGHT TRUCK']);
        CustomerGroup::create(['name' => 'MATI1-MOTORCYCLE']);
        CustomerGroup::create(['name' => 'MATI1-PRIVATE VEHICLE']);
        CustomerGroup::create(['name' => 'MATI1-PUV/PUJ/MULTICAB']);
        CustomerGroup::create(['name' => 'MATI1-TRUCK']);
    }
}
