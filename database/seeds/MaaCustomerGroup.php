<?php

use App\CustomerGroup;
use Illuminate\Database\Seeder;

class MaaCustomerGroup extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        CustomerGroup::create(['name' => 'MAA-MOTORCYCLE']);
        CustomerGroup::create(['name' => 'MAA-MULTICAB']);
        CustomerGroup::create(['name' => 'MAA-TRUCK']);
        CustomerGroup::create(['name' => 'MAA-CONTAINER']);
        CustomerGroup::create(['name' => 'MAA-PRIVATE']);
        CustomerGroup::create(['name' => 'MAA-PUJ']);
        CustomerGroup::create(['name' => 'MAA-TAXI']);
        CustomerGroup::create(['name' => 'MAA-LIGHT TRUCK']);
    }
}
