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
        //Perfromance rules to add to system
        $rules = [
            array(
                'rule' => 'admin',
                'desc' =>'Admin',
                'for' => 'Admin Only',
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
