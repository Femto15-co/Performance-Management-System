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
                'rule' => 'Sprint Delivery',
                'desc' =>'Delivering sprints on time.',
                'employee_type' => $developer->id,
                'weight' => 2
            ),
            array(
                'rule' => 'Attendance',
                'desc' =>'Showing up on time for scrum meetings and avoiding unnecessary absence.',
                'employee_type' => $developer->id,
                'weight' => 2
            ),
            array(
                'rule' => 'Self Learning',
                'desc' =>'Learning new skills that would help the company.',
                'employee_type' => $developer->id,
                'weight' => 1
            ),
            array(
                'rule' => 'Teamwork',
                'desc' =>'Working well with other teammates and helping other teammates.',
                'employee_type' => $developer->id,
                'weight' => 2
            ),
            array(
                'rule' => 'Behavior',
                'desc' =>'Being polite and respectful with other company members.',
                'employee_type' => $developer->id,
                'weight' => 2
            ),
            array(
                'rule' => 'Communication',
                'desc' =>'Communicating with other company members and reaching out for help or advice when needed. Also repotring progress.',
                'employee_type' => $developer->id,
                'weight' => 2
            ),
            array(
                'rule' => 'Code Efficiency',
                'desc' =>"Writing code that doesn't waste resources.",
                'employee_type' => $developer->id,
                'weight' => 2
            ),
            array(
                'rule' => 'Documentation',
                'desc' =>'Is the code well commented and functions well documented.',
                'employee_type' => $developer->id,
                'weight' => 2
            ),
            array(
                'rule' => 'Security Awareness',
                'desc' =>'Is the written code secure? Does it have the minimum number of security issue?',
                'employee_type' => $developer->id,
                'weight' => 1
            ),
            array(
                'rule' => 'Testing Skills',
                'desc' =>'Is the written code working? Does it have the minimum number of bugs?',
                'employee_type' => $developer->id,
                'weight' => 2
            ),
            array(
                'rule' => 'Troubleshooting Skills',
                'desc' =>'The ability to find the bug cause and solving it on their own.',
                'employee_type' => $developer->id,
                'weight' => 3
            ),

            array(
                'rule' => 'Problem Solving Skills',
                'desc' =>'Being able to find an effeicient solution for any given problem.',
                'employee_type' => $developer->id,
                'weight' => 3
            ),
            //Designer Rules
            array(
                'rule' => 'Task Delivery',
                'desc' =>'Delivering tasks on time.',
                'employee_type' => $designer->id,
                'weight' => 2
            ),
            array(
                'rule' => 'Attendance',
                'desc' =>'Showing up on time for meetings and avoiding unnecessary absence.',
                'employee_type' => $designer->id,
                'weight' => 2
            ),
            array(
                'rule' => 'Communication',
                'desc' =>'Communicating with other company members. Also repotring progress.',
                'employee_type' => $designer->id,
                'weight' => 2
            ),
            //Designer rules
            array(
                'rule' => 'Creativity',
                'desc' =>'Using imagination to create original and catching designs.',
                'employee_type' => $designer->id,
                'weight' => 3
            ),

            //Designer rules
            array(
                'rule' => 'Following Standerds',
                'desc' =>'Alignment Principles, UX Standers etc.',
                'employee_type' => $designer->id,
                'weight' => 3
            ),
            array(
                'rule' => 'Positive Feedback',
                'desc' =>'How often a client positive feedback is given.',
                'employee_type' => $designer->id,
                'weight' => 1
            ),

        ];

        //Add to DB if not exists
        foreach($rules as $rule)
        {
            App\PerformanceRule::firstOrCreate($rule);
        }
    }
}
