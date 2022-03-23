<?php

use App\CustomerGroup;
use Illuminate\Database\Seeder;

class CGGENSAN extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        CustomerGroup::create(['name' => 'GSC-MOTORCYCLE']);
        CustomerGroup::create(['name' => 'GSC-GTA(UHAW)']);
        CustomerGroup::create(['name' => 'GSC-PRIVATE VEHICLE']);
        CustomerGroup::create(['name' => 'GSC-TAXI/MULTICAB']);
        CustomerGroup::create(['name' => 'GSC-TRUCK']);
    }
}
