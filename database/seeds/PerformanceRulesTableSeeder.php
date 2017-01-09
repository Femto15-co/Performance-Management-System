<?php

use Illuminate\Database\Seeder;


class PerformanceRulesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
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

        //Performance rules to add to system
        $rules = [
            //developer rules
            array(
                'rule' => 'Troubleshooting',
                'desc' =>'Troubleshooting',
                'employee_type' => $developer->id,
                'weight' => 5
            ),

            array(
                'rule' => 'Problem solving',
                'desc' =>'Problem Solving',
                'employee_type' => $developer->id,
                'weight' => 4
            ),

            //Designer rules
            array(
                'rule' => 'Creativity',
                'desc' =>'Creativity',
                'employee_type' => $designer->id,
                'weight' => 5
            ),

            //Designer rules
            array(
                'rule' => 'Innovation',
                'desc' =>'Innovation',
                'employee_type' => $designer->id,
                'weight' => 4
            ),

        ];

        //Add to DB if not exists
        foreach($rules as $rule)
        {
            App\PerformanceRule::firstOrCreate($rule);
        }
    }
}
