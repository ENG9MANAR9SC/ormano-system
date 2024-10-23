<?php

namespace Database\Seeders;

use App\Models\AppModel;
use App\Models\Currency;
use App\Models\Country;
use Database\Factories\CurrencyFactory;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$currencyData = [
			[
				'titles'          => 'Syrian Pound',
				'descriptions'    => 'Syrian Pound',
				'currency_symbol' => 'SP',
				'iso_code'        => 'SYP',
			],
			[
				'titles'          => 'Iraqi Dinar',
				'descriptions'    => 'Iraqi Dinar',
				'currency_symbol' => 'IQD',
				'iso_code'        => 'IQD',
			],
			[
				'titles'          => 'Euro',
				'descriptions'    => 'Euro',
				'currency_symbol' => '€',
				'iso_code'        => 'EUR',
			],
			[
				'titles'          => 'Dollar',
				'descriptions'    => 'Dollar',
				'currency_symbol' => '$',
				'iso_code'        => 'USD',
			],

		];

		foreach ($currencyData as $currency) {
			Currency::create($currency);
		}

	}
}
