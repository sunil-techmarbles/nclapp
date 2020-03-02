<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin_data = [
            'first_name' => 'Admin',
            'last_name' => 'Admin',
            'email'    => 'admin@site.com',
            'password' => 123456789,
        ];
        $admin = Sentinel::registerAndActivate($admin_data);
        $admin_role = Sentinel::findRoleByslug('admin');
        $admin_role->users()->attach($admin);
    }
    
}
 