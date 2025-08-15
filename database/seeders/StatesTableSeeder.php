<?php

namespace Database\Seeders;

use App\State;
use Illuminate\Database\Seeder;

class StatesTableSeeder extends Seeder
{
    public function run()
    {
        $states = [[
            'id' => 1,
            'lang_de' => 'Baden-Württemberg',
            'country' => 'DE',
            'code' => 'DE-BW',
        ],
            [
                'id' => 2,
                'lang_de' => 'Bayern',
                'country' => 'DE',
                'code' => 'DE-BY',
            ],
            [
                'id' => 3,
                'lang_de' => 'Berlin',
                'country' => 'DE',
                'code' => 'DE-BE',
            ],
            [
                'id' => 4,
                'lang_de' => 'Brandenburg',
                'country' => 'DE',
                'code' => 'DE-BB',
            ],
            [
                'id' => 5,
                'lang_de' => 'Bremen',
                'country' => 'DE',
                'code' => 'DE-HB',
            ],
            [
                'id' => 6,
                'lang_de' => 'Hamburg',
                'country' => 'DE',
                'code' => 'DE-HH',
            ],
            [
                'id' => 7,
                'lang_de' => 'Hessen',
                'country' => 'DE',
                'code' => 'DE-HE',
            ],
            [
                'id' => 8,
                'lang_de' => 'Mecklenburg-Vorpommern',
                'country' => 'DE',
                'code' => 'DE-MV',
            ],
            [
                'id' => 9,
                'lang_de' => 'Niedersachsen',
                'country' => 'DE',
                'code' => 'DE-NI',
            ],
            [
                'id' => 10,
                'lang_de' => 'Nordrhein-Westfalen',
                'country' => 'DE',
                'code' => 'DE-NW',
            ],
            [
                'id' => 11,
                'lang_de' => 'Rheinland-Pfalz',
                'country' => 'DE',
                'code' => 'DE-RP',
            ],
            [
                'id' => 12,
                'lang_de' => 'Saarland',
                'country' => 'DE',
                'code' => 'DE-SL',
            ],
            [
                'id' => 13,
                'lang_de' => 'Sachsen',
                'country' => 'DE',
                'code' => 'DE-SN',
            ],
            [
                'id' => 14,
                'lang_de' => 'Sachsen-Anhalt',
                'country' => 'DE',
                'code' => 'DE-ST',
            ],
            [
                'id' => 15,
                'lang_de' => 'Schleswig-Holstein',
                'country' => 'DE',
                'code' => 'DE-SH',
            ],
            [
                'id' => 16,
                'lang_de' => 'Thüringen',
                'country' => 'DE',
                'code' => 'DE-TH',
            ],

            [
                'id' => 17,
                'lang_de' => 'Burgenland',
                'country' => 'AT',
                'code' => 'AT-1',
            ],
            [
                'id' => 18,
                'lang_de' => 'Kärnten',
                'country' => 'AT',
                'code' => 'AT-2',
            ],
            [
                'id' => 19,
                'lang_de' => 'Niederösterreich',
                'country' => 'AT',
                'code' => 'AT-3',
            ],
            [
                'id' => 20,
                'lang_de' => 'Oberösterreich',
                'country' => 'AT',
                'code' => 'AT-4',
            ],
            [
                'id' => 21,
                'lang_de' => 'Salzburg',
                'country' => 'AT',
                'code' => 'AT-5',
            ],
            [
                'id' => 22,
                'lang_de' => 'Steiermark',
                'country' => 'AT',
                'code' => 'AT-6',
            ],
            [
                'id' => 23,
                'lang_de' => 'Tirol',
                'country' => 'AT',
                'code' => 'AT-7',
            ],
            [
                'id' => 24,
                'lang_de' => 'Voralberg',
                'country' => 'AT',
                'code' => 'AT-8',
            ],
            [
                'id' => 25,
                'lang_de' => 'Wien',
                'country' => 'AT',
                'code' => 'AT-9',
            ],
        ];

        State::insert($states);
    }
}
