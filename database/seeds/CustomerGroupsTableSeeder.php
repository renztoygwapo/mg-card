<?php

use App\CustomerGroup;
use Illuminate\Database\Seeder;

class CustomerGroupsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        CustomerGroup::create(['name' => 'CAL-TRUCK']);
        CustomerGroup::create(['name' => 'CAL-CONTAINER']);
        CustomerGroup::create(['name' => 'CAL-HYBRID']);
        CustomerGroup::create(['name' => 'CAL-PRIVATE']);
        CustomerGroup::create(['name' => 'CAL-MOTORCYCLE']);
        CustomerGroup::create(['name' => 'CAL-ALL ABOARD']);
        CustomerGroup::create(['name' => 'CAL-PUJ/PUV']);
        CustomerGroup::create(['name' => 'CAL-TDTC']);
        CustomerGroup::create(['name' => 'CAL-PARISH']);
        CustomerGroup::create(['name' => 'CAL-TRANSCO']);
        CustomerGroup::create(['name' => 'CAL-TAXI/MULTICAB(GASOLINA)']);
        CustomerGroup::create(['name' => 'CAL-UNICO']);
    }
}
