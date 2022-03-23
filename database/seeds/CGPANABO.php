<?php

use App\CustomerGroup;
use Illuminate\Database\Seeder;

class CGPANABO extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        CustomerGroup::create(['name' => 'PNBO-MOTORCYCLE']);
        CustomerGroup::create(['name' => 'PNBO-PRIVATE VEHICLE']);
        CustomerGroup::create(['name' => 'PNBO-PUJ/PUV']);
        CustomerGroup::create(['name' => 'PNBO-TAXI/MULTICAB']);
        CustomerGroup::create(['name' => 'PNBO-TRUCKS']);
    }
}
