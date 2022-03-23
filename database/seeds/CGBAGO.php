<?php

use Illuminate\Database\Seeder;
use App\CustomerGroup;

class CGBAGO extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        CustomerGroup::create(['name' => 'BAGO-MOTORCYCLE']);
        CustomerGroup::create(['name' => 'BAGO-TRUCK']);
        CustomerGroup::create(['name' => 'BAGO-PRIVATE']);
        CustomerGroup::create(['name' => 'BAGO-TAXI/MULTICAB']);
        CustomerGroup::create(['name' => 'BAGO-PUJ/PUV']);
    }
}
