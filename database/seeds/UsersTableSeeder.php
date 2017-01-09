<?php

use Illuminate\Database\Seeder;
use App\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Add admin to system.
     *
     * @return void
     */
    public function run()
    {

        //Get employee_type Admin ID
        $admin = \App\EmployeeType::whereType('Admin')->first();
        if(!$admin)
            return false;

        //Get employee_type Developer ID
        $developer = \App\EmployeeType::whereType('Developer')->first();
        if(!$developer)
            return false;

        //Get employee_type Designer ID
        $designer = \App\EmployeeType::whereType('Designer')->first();
        if(!$designer)
            return false;

        //Add users to system
        $users = [
            array(
                'name' => 'Admin',
                'email' =>'admin@admin.com',
                'password' => bcrypt('admin'),
                'employee_type' => $admin->id
            ),

            array(
                'name' => 'Developer',
                'email' =>'developer@developer.com',
                'password' => bcrypt('developer'),
                'employee_type' => $developer->id
            ),

            array(
                'name' => 'Designer',
                'email' =>'designer@designer.com',
                'password' => bcrypt('designer'),
                'employee_type' => $designer->id
            ),
        ];

        //Add to DB if not exists
        foreach($users as $user)
        {
            User::firstOrCreate($user);
        }
    }
}
