<?php

use Illuminate\Database\Seeder;
use App\CustomerGroup;

class CGMINTAL extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        CustomerGroup::create(['name' => 'MNTL-MOTORCYCLE']);
        CustomerGroup::create(['name' => 'MNTL-PRIVATE VEHICLE']);
        CustomerGroup::create(['name' => 'MNTL-PUJ/PUV']);
        CustomerGroup::create(['name' => 'MNTL-TAXI/MULTICAB']);
        CustomerGroup::create(['name' => 'MNTL-TRUCK']);
    }
}
