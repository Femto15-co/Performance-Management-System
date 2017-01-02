<?php

use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Roles to add to system
        $roles = [
            array(
                'name' => 'admin',
                'display_name' =>'Admin',
                'description' => 'Admin Only'
            ),

            array(
                'name' => 'employee',
                'display_name' =>'Employee',
                'description' => 'Employee Only'
            ),
        ];

        //Add to DB if not exists
        foreach($roles as $role)
        {
            App\Role::firstOrCreate($role);
        }
    }
}
