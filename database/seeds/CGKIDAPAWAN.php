<?php

use Illuminate\Database\Seeder;
use App\CustomerGroup;

class CGKIDAPAWAN extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        CustomerGroup::create(['name' => 'K1-MOTORCYCLE']);
        CustomerGroup::create(['name' => 'K1-MULTICAB']);
        CustomerGroup::create(['name' => 'K1-PRIVATE VEHICLE']);
        CustomerGroup::create(['name' => 'K1-PUJ/PUV']);
        CustomerGroup::create(['name' => 'K1-TRUCK']);
    }
}
