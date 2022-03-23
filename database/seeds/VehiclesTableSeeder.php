<?php

use App\Vehicle;
use Illuminate\Database\Seeder;

class VehiclesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Vehicle::create(['vehicle_name' => 'PRIVATE VEHICLE']);
        Vehicle::create(['vehicle_name' => 'PUJ/PUV/TAXI/MULTICAB']);
        Vehicle::create(['vehicle_name' => 'MOTORCYLE']);
        Vehicle::create(['vehicle_name' => 'LIGHT TRUCK']);
        Vehicle::create(['vehicle_name' => 'HEAVY TRUCK']);
        Vehicle::create(['vehicle_name' => 'HYBRID']);
    }
}
