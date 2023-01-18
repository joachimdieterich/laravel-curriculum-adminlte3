<?php

namespace Database\Seeders;

use App\Medium;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class MediaTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (File::exists(storage_path('app/subjects'))) {
            $files = File::allFiles(storage_path('app/subjects'));
            foreach ($files as $file) {
                $media = new Medium([
                    'path' => '/subjects/',
                    'title' => $file->getFilename(),
                    'medium_name' => $file->getFilename(),
                    'description' => '',
                    'author' => 'admin',
                    'publisher' => 'admin',
                    'city' => '',
                    'date' => '2019-09-19 17:52:32',
                    'size' => $file->getSize(),
                    'mime_type' => $file->getType(),
                    'license_id' => 3,
                    'owner_id' => 1,

                ]);
                $media->save();
            }
        }
    }
}
