<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;
use PragmaRX\Countries\Package\Services\Countries;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		$countryData = [
			[
				'names' => 'Syria',
				'currency_id' => 1,
				'languages' => 'Arabic',
				'zone_id' => 1,
			],
			
			[
				'names' => 'Iraq',
				'currency_id' => 2,
				'languages' => 'Arabic',
				'zone_id' => 2,
			],
			[
				'names' => 'Germany',
				'currency_id' => 3,
				'languages' => 'German',
				'zone_id' => 3,
			],
			[
				'names' => 'United States',
				'currency_id' => 4,
				'languages' => 'English',
				'zone_id' => 4,
			],
		];
		
		foreach ($countryData as $country) {
			Country::create($country);
		}
			
				
    }
}
