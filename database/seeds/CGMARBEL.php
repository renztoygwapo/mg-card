<?php

use Illuminate\Database\Seeder;
use App\CustomerGroup;

class CGMARBEL extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        CustomerGroup::create(['name' => 'MRBL-MOTORCYCLE']);
        CustomerGroup::create(['name' => 'MRBL-TRUCK']);
        CustomerGroup::create(['name' => 'MRBL-PRIVATE VEHICLE']);
    }
}
