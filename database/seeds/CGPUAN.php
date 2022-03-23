<?php

use Illuminate\Database\Seeder;
use App\CustomerGroup;

class CGPUAN extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        CustomerGroup::create(['name' => 'PUAN-MOTORCYCLE']);
        CustomerGroup::create(['name' => 'PUAN-TRUCK']);
        CustomerGroup::create(['name' => 'PUAN-PRIVATE']);
        CustomerGroup::create(['name' => 'PUAN-PUJ/PUV']);
        CustomerGroup::create(['name' => 'PUAN-TAXI/MULTICAB']);
    }
}