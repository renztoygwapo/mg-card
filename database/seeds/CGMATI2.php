<?php

use Illuminate\Database\Seeder;
use App\CustomerGroup;

class CGMATI2 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        CustomerGroup::create(['name' => 'MATI2-LIGHT TRUCK']);
        CustomerGroup::create(['name' => 'MATI2-MOTORCYCLE']);
        CustomerGroup::create(['name' => 'MATI2-PRIVATE VEHICLE']);
        CustomerGroup::create(['name' => 'MATI2-PUV/PUJ/MULTICAB']);
        CustomerGroup::create(['name' => 'MATI2-TRUCK']);
    }
}
