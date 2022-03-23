<?php

use Illuminate\Database\Seeder;
use App\CustomerGroup;

class CGTAGUM2 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        CustomerGroup::create(['name' => 'TAG2-MOTORCYCLE']);
        CustomerGroup::create(['name' => 'TAG2-MULTICAB']);
        CustomerGroup::create(['name' => 'TAG2-PRIVATE VEHICLE']);
        CustomerGroup::create(['name' => 'TAG2-TRUCK']);
        CustomerGroup::create(['name' => 'TAG2-VAN']);
    }
}
