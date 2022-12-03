<?php
namespace Database\Seeders;
use App\OrganizationType;
use Illuminate\Database\Seeder;

class OrganizationTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $organizationType = [[
            'id'=>1,
            'title'=>'Schulartübergreifend',
            'external_id'=>0,
            'state_id'       => 'DE-RP',
            'country_id'     => 'DE',
        ],
            [
                'id'=>2,
                'title'=>'Kindergarten',
                'external_id'=>1,
                'state_id'       => 'DE-RP',
                'country_id'     => 'DE',
            ],
            [
                'id'=>3,
                'title'=>'Schulkindergarten',
                'external_id'=>1,
                'state_id'       => 'DE-RP',
                'country_id'     => 'DE',
            ],
            [
                'id'=>4,
                'title'=>'Förderschulkindergarten',
                'external_id'=>1,
                'state_id'       => 'DE-RP',
                'country_id'     => 'DE',
            ],
            [
                'id'=>5,
                'title'=>'Grundschule - Primarstufe',
                'external_id'=>2,
                'state_id'       => 'DE-RP',
                'country_id'     => 'DE',
            ],
            [
                'id'=>6,
                'title'=>'Förderschule',
                'external_id'=>3,
                'state_id'       => 'DE-RP',
                'country_id'     => 'DE',
            ],
            [
                'id'=>7,
                'title'=>'Schule mit dem Förderschwerpunkt Lernen',
                'external_id'=>3,
                'state_id'       => 'DE-RP',
                'country_id'     => 'DE',
            ],
            [
                'id'=>8,
                'title'=>'Schule mit dem Förderschwerpunkt ganzheitliche Entwicklung',
                'external_id'=>3,
                'state_id'       => 'DE-RP',
                'country_id'     => 'DE',
            ],
            [
                'id'=>9,
                'title'=>'Schule mit dem Förderschwerpunkt motorische Entwicklung',
                'external_id'=>3,
                'state_id'       => 'DE-RP',
                'country_id'     => 'DE',
            ],
            [
                'id'=>10,
                'title'=>'Schule mit dem Förderschwerpunkt sozial-emotionale Entwicklung',
                'external_id'=>3,
                'state_id'       => 'DE-RP',
                'country_id'     => 'DE',
            ],
            [
                'id'=>11,
                'title'=>'Schule für Blinde und Sehbehinderte',
                'external_id'=>3,
                'state_id'       => 'DE-RP',
                'country_id'     => 'DE',
            ],
            [
                'id'=>12,
                'title'=>'Schule für Gehörlose und Schwerhörige',
                'external_id'=>3,
                'state_id'       => 'DE-RP',
                'country_id'     => 'DE',
            ],
            [
                'id'=>13,
                'title'=>'Schule mit dem Förderschwerpunkt Sprache',
                'external_id'=>3,
                'state_id'       => 'DE-RP',
                'country_id'     => 'DE',
            ],
            [
                'id'=>14,
                'title'=>'Förderzentrum',
                'external_id'=>3,
                'state_id'       => 'DE-RP',
                'country_id'     => 'DE',
            ],
            [
                'id'=>15,
                'title'=>'Schulartübergreifende Orientierungsstufe (UEOS)',
                'external_id'=>4,
                'state_id'       => 'DE-RP',
                'country_id'     => 'DE',
            ],
            [
                'id'=>16,
                'title'=>'Hauptschule',
                'external_id'=>5,
                'state_id'       => 'DE-RP',
                'country_id'     => 'DE',
            ],
            [
                'id'=>17,
                'title'=>'Realschule',
                'external_id'=>6,
                'state_id'       => 'DE-RP',
                'country_id'     => 'DE',
            ],
            [
                'id'=>18,
                'title'=>'Realschule Plus',
                'external_id'=>7,
                'state_id'       => 'DE-RP',
                'country_id'     => 'DE',
            ],
            [
                'id'=>19,
                'title'=>'Gymnasium - Sekundarstufe I (G8)',
                'external_id'=>8,
                'state_id'       => 'DE-RP',
                'country_id'     => 'DE',
            ],
            [
                'id'=>20,
                'title'=>'Gymnasium - Sekundarstufe I (G9)',
                'external_id'=>9,
                'state_id'       => 'DE-RP',
                'country_id'     => 'DE',
            ],
            [
                'id'=>21,
                'title'=>'Integrierte Gesamtschule - Sekundarstufe I',
                'external_id'=>10,
                'state_id'       => 'DE-RP',
                'country_id'     => 'DE',
            ],
            [
                'id'=>22,
                'title'=>'Schulartübergreifende Sekundarstufe I',
                'external_id'=>11,
                'state_id'       => 'DE-RP',
                'country_id'     => 'DE',
            ],
            [
                'id'=>23,
                'title'=>'Gymnasium - Sekundarstufe II (G8)',
                'external_id'=>12,
                'state_id'       => 'DE-RP',
                'country_id'     => 'DE',
            ],
            [
                'id'=>24,
                'title'=>'Gymnasium - Sekundarstufe II (G9)',
                'external_id'=>13,
                'state_id'       => 'DE-RP',
                'country_id'     => 'DE',
            ],
            [
                'id'=>25,
                'title'=>'Integrierte Gesamtschule - Sekundarstufe II',
                'external_id'=>14,
                'state_id'       => 'DE-RP',
                'country_id'     => 'DE',
            ],
            [
                'id'=>26,
                'title'=>'Schulartübergreifende Sekundarstufe II',
                'external_id'=>15,
                'state_id'       => 'DE-RP',
                'country_id'     => 'DE',
            ],
            [
                'id'=>27,
                'title'=>'Ganztagsschule',
                'external_id'=>16,
                'state_id'       => 'DE-RP',
                'country_id'     => 'DE',
            ],
            [
                'id'=>28,
                'title'=>'Berufsbildende Schule',
                'external_id'=>17,
                'state_id'       => 'DE-RP',
                'country_id'     => 'DE',
            ],
            [
                'id'=>29,
                'title'=>'Freie Waldorfschule - Primarstufe',
                'external_id'=>94,
                'state_id'       => 'DE-RP',
                'country_id'     => 'DE',
            ],
            [
                'id'=>30,
                'title'=>'Freie Waldorfschule - Sekundarstufe I',
                'external_id'=>95,
                'state_id'       => 'DE-RP',
                'country_id'     => 'DE',
            ],
            [
                'id'=>31,
                'title'=>'Freie Waldorfschule - Sekundarstufe II',
                'external_id'=>96,
                'state_id'       => 'DE-RP',
                'country_id'     => 'DE',
            ],
            [
                'id'=>32,
                'title'=>'Kolleg - Sekundarstufe II',
                'external_id'=>97,
                'state_id'       => 'DE-RP',
                'country_id'     => 'DE',
            ],
            [
                'id'=>33,
                'title'=>'Abendgymnasium - Sekundarstufe II',
                'external_id'=>98,
                'state_id'       => 'DE-RP',
                'country_id'     => 'DE',
            ],
            [
                'id'=>34,
                'title'=>'Fachoberschule an der Realschule Plus',
                'external_id'=>99,
                'state_id'       => 'DE-RP',
                'country_id'     => 'DE',
            ],
        ];

        OrganizationType::insert($organizationType);
    }
}
