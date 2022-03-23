<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesPermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        app()['cache']->forget('spatie.permission.cache');
         // create permissions admin
         Permission::create(['name' => 'customer registration', 'guard_name' => 'api']);
         Permission::create(['name' => 'points conversion', 'guard_name' => 'api']);
         Permission::create(['name' => 'transaction', 'guard_name' => 'api']);
         Permission::create(['name' => 'points conversion summary', 'guard_name' => 'api']);
         Permission::create(['name' => 'view accumulated points', 'guard_name' => 'api']);
         Permission::create(['name' => 'user management', 'guard_name' => 'api']);
         Permission::create(['name' => 'price management', 'guard_name' => 'api']);
         Permission::create(['name' => 'export database', 'guard_name' => 'api']);
         Permission::create(['name' => 'card management', 'guard_name' => 'api']);

         // create roles and assign created permissions
         $role = Role::create(['name' => 'forecourt_attendant', 'guard_name' => 'api']);
         $role->givePermissionTo('transaction');

         $role = Role::create(['name' => 'sic', 'guard_name' => 'api']);
         $role->givePermissionTo(
             [
                 'customer registration',
                 'points conversion',
                 'transaction',
                 'points conversion summary',
                 'view accumulated points'
             ]);
         
         $role = Role::create(['name' => 'admin', 'guard_name' => 'api']);
         $role->givePermissionTo(Permission::where('guard_name','api')->get());
    }
}
