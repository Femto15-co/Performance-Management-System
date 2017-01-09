<?php

use Illuminate\Database\Seeder;
use App\Defect;

class DefectsTableSeeder extends Seeder {
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		$defects = [
			[
				'title' => 'Not showing up on daily scrum.',
				'score' => '2',
			],
			[
				'title' => 'Violating any coding guideline.',
				'score' => '1',
			],
			[
				'title' => 'Delivering a code that doesn’t match the requirements.',
				'score' => '1',
			],
			[
				'title' => 'Delivering a code that has a straightforward bug that could be avoided.',
				'score' => '2',
			],
			[
				'title' => 'Misbehaving with other team members.',
				'score' => '4',
			],
			[
				'title' => 'Showing one hour late.',
				'score' => '6',
			],
			[
				'title' => 'Not showing without a notification.',
				'score' => '12',
			],

		];
		//Create the records if they don't already exist
		foreach ($defects as $defect) {
			Defect::firstOrCreate($defect);
		}
	}
}
