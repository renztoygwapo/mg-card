<?php

use Illuminate\Database\Seeder;
use App\CustomerGroup;

class CGDIGOS1 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        CustomerGroup::create(['name' => 'D1-MOTORCYCLE']);
        CustomerGroup::create(['name' => 'D1-PRIVATE VEHICLE']);
        CustomerGroup::create(['name' => 'D1-PUJ/PUV']);
        CustomerGroup::create(['name' => 'D1-TRUCK']);
    }
}
