<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void 
     */
    public function run()
    {
        //$this->call(CustomerGroupsTableSeeder::class);
        $this->call(RolesPermissionTableSeeder::class);
        $this->call(VehiclesTableSeeder::class);
        // $this->call(AuthClientsTableSeeder::class);
        $this->call(UserAdminAccountSeeder::class);
    }
}
