<?php

use Illuminate\Database\Seeder;
use App\CustomerGroup;

class CGCATALUNAN extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        CustomerGroup::create(['name' => 'CAT-PRIVATE VEHICLE']);
        CustomerGroup::create(['name' => 'CAT-PUJ/PUV']);
        CustomerGroup::create(['name' => 'CAT-TAXI/MULTICAB']);
        CustomerGroup::create(['name' => 'CAT-TRICYCLE']);
    }
}
