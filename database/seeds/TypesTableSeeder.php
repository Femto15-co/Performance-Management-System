<?php

use Illuminate\Database\Seeder;
use App\EmployeeType;

class TypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Types to add to system
        $types = [
            array(
                'type' => 'Developer',
            ),

            array(
                'type' => 'Designer',
            ),
        ];

        //Add to DB if not exists
        foreach($types as $type)
        {
            App\EmployeeType::firstOrCreate($type);
        }
    }
}
