<?php
namespace Database\Seeders;
use App\StatusDefinition;
use Illuminate\Database\Seeder;

class StatusDefinitionsTableSeeder extends Seeder
{
    public function run()
    {
        $status_definitions = [
            [
                'id'=>1,
                'status_definition_id'=>0,
                'color_css_class'=> 'gray',
                'lang_en'=>'deactivated',
                'lang_de'=>'deaktiviert',
            ],
            [
                'id'=>2,
                'status_definition_id'=>1,
                'color_css_class'=> 'success',
                'lang_en'=>'active',
                'lang_de'=>'aktiv',
            ],
            [
                'id'=>3,
                'status_definition_id'=>2,
                'color_css_class'=> 'warning',
                'lang_en'=>'pending activation',
                'lang_de'=>'Warte auf Aktivierung',
            ],
            [
                'id'=>4,
                'status_definition_id'=>3,
                'color_css_class'=> 'info',
                'lang_en'=>'reset password',
                'lang_de'=>'Passwort zurücksetzen',
            ],
            [
                'id'=>5,
                'status_definition_id'=>4,
                'color_css_class'=> 'danger',
                'lang_en'=>'locked',
                'lang_de'=>'gesperrt',
            ],
            [
                'id'=>6,
                'status_definition_id'=>5,
                'color_css_class'=> 'danger',
                'lang_en'=>'todo',
                'lang_de'=>'todo',
            ],
            [
                'id'=>7,
                'status_definition_id'=>6,
                'color_css_class'=> 'success',
                'lang_en'=>'done',
                'lang_de'=>'erledigt',
            ],
        ];

        StatusDefinition::insert($status_definitions);
    }
}
