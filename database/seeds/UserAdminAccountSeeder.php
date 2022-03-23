<?php

use App\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class UserAdminAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::query()->create([
            'email' => 'admin@mygas.test',
            'username' => 'admin',
            'password' => bcrypt('admin123'),
        ]);
        // assign user as admin
        $user->syncRoles('admin');
        echo "Admin Created";
    }
}
