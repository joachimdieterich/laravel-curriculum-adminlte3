<?php
namespace Database\Seeders;
use App\Curriculum;
use Illuminate\Database\Seeder;

class CurriculumGroupTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Curriculum::findOrFail(1)->groups()->sync(1);
    }
}
