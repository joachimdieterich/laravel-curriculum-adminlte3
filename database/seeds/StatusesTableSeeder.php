<?php

use App\Status;
use Illuminate\Database\Seeder;

class StatusesTableSeeder extends Seeder
{
     public function run()
    {
        $statuses = [
                        [
                        'id'=>1,
                        'status_id'=>0,
                        'color_css_class'=> 'gray',
                        'lang_en'=>'deactivated',
                        'lang_de'=>'deaktiviert',
                        ],       
                        [
                        'id'=>2,
                        'status_id'=>1,
                        'color_css_class'=> 'success',
                        'lang_en'=>'active',
                        'lang_de'=>'aktiv',
                        ],         
                        [
                        'id'=>3,
                        'status_id'=>2,
                        'color_css_class'=> 'warning',
                        'lang_en'=>'pending activation',
                        'lang_de'=>'Warte auf Aktivierung',
                        ],       
                        [
                        'id'=>4,
                        'status_id'=>3, 
                        'color_css_class'=> 'info',
                        'lang_en'=>'reset password',
                        'lang_de'=>'Passwort zurücksetzen',
                        ],       
                        [
                        'id'=>5,
                        'status_id'=>4, 
                        'color_css_class'=> 'danger',
                        'lang_en'=>'locked',
                        'lang_de'=>'gesperrt',
                        ],                
            ];

            Status::insert($statuses);
    }
}
