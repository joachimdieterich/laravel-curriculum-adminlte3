<?php
namespace Database\Seeders;
use App\License;
use Illuminate\Database\Seeder;

class LicensesTableSeeder extends Seeder
{
    public function run()
    {
        $licenses = [
            [
                'id'=>1,
                'lang_en'=>'other',
                'lang_de'=>'Sonstige',
                'license_url' => '2019-04-15 19:14:42',
            ],
            [
                'id'=>2,
                'lang_en'=>'All rights reserved',
                'lang_de'=>'Alle Rechte vorbehalt',
                'license_url' => '2019-04-15 19:14:42',
            ],
            [
                'id'=>3,
                'lang_en'=>'Public Domain / CC0',
                'lang_de'=>'Public Domain / CC0',
                'license_url' => '2019-04-15 19:14:42',
            ],
            [
                'id'=>4,
                'lang_en'=>'CC BY',
                'lang_de'=>'CC BY',
                'license_url' => '2019-04-15 19:14:42',
            ],
            [
                'id'=>5,
                'lang_en'=>'CC BY-ND',
                'lang_de'=>'CC BY-ND keine Bearbeitung',
                'license_url' => '2019-04-15 19:14:42',
            ],
            [
                'id'=>6,
                'lang_en'=>'CC BY-NC-ND',
                'lang_de'=>'CC BY-NC-ND keine kommerzielle Nutzung - keine Bearbeitung',
                'license_url' => '2019-04-15 19:14:42',
            ],
            [
                'id'=>7,
                'lang_en'=>'CC BY-NC',
                'lang_de'=>'CC BY-NC keine kommerzielle Nutzung',
                'license_url' => '2019-04-15 19:14:42',
            ],
            [
                'id'=>8,
                'lang_en'=>'CC BY-NC-SA',
                'lang_de'=>'CC BY-NC-SA keine kommerzielle Nutzung - Weitergabe unter gleichen Bedingungen',
                'license_url' => '2019-04-15 19:14:42',
            ],
            [
                'id'=>9,
                'lang_en'=>'CC BY-SA',
                'lang_de'=>'CC BY-SA Weitergabe unter gleichen Bedingungen',
                'license_url' => '2019-04-15 19:14:42',
            ],

        ];

        License::insert($licenses);
    }
}
