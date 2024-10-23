<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $cityData = [
            [
                'id'         => 1,
                'country_id' => 1,
                'iso_code'   => 'DI',
                'name'       => 'Damascus',
                'active'     => 1,
            ],
            [
                'id'         => 2,
                'country_id' => 1,
                'iso_code'   => 'DR',
                'name'       => 'Daraa',
                'active'     => 1,
            ],
            [
                'id'         => 3,
                'country_id' => 1,
                'iso_code'   => 'DY',
                'name'       => 'Deir ez-Zor',
                'active'     => 1,
            ],
            [
                'id'         => 4,
                'country_id' => 1,
                'iso_code'   => 'HM',
                'name'       => 'Hama',
                'active'     => 1,
            ],
            [
                'id'         => 5,
                'country_id' => 1,
                'iso_code'   => 'HI',
                'name'       => 'Homs',
                'active'     => 1,
            ],
           [
                'id'         => 6,
                'country_id' => 1,
                'iso_code'   => 'ID',
                'name'       => 'Idlib',
                'active'     => 1,
           ],
            [
                'id'         => 7,
                'country_id' => 1,
                'iso_code'   => 'LA',
                'name'       => 'Latakia',
                'active'     => 1,
            ],
            [
                'id'         => 8,
                'country_id' => 1,
                'iso_code'   => 'QU',
                'name'       => 'Quneitra',
                'active'     => 1,
            ],
            [
                'id'         => 9,
                'country_id' => 1,
                'iso_code'   => 'RD',
                'name'       => 'Rif Dimashq',
                'active'     => 1,
            ],
            [
                'id'         => 10,
                'country_id' => 1,
                'iso_code'   => 'TA',
                'name'       => 'Tartus',
                'active'     => 1,
            ],
        ];

        foreach ($cityData as $city) {
            City::create($city);
        }
    }
}
