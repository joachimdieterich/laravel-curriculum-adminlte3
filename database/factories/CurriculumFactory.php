<?php

namespace Database\Factories;

use App\Curriculum;
use Illuminate\Database\Eloquent\Factories\Factory;

class CurriculumFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Curriculum::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title'                 => $this->faker->company,
            'description'           => $this->faker->paragraph,

            'author'                => $this->faker->userName,
            'publisher'             => $this->faker->company,
            'city'                  => $this->faker->city,
            'date'                  => $this->faker->dateTimeThisMonth()->format('Y-m-d H:i:s'),

            'color'                 => $this->faker->rgbColor,

            'grade_id' => 1,
            'subject_id' => 1,

            'state_id' => 'DE-RP',
            'country_id' => 'DE',
            'organization_type_id' => 1,
            'type_id' => 1,

            'medium_id' => null, //define std. image

            'owner_id' => auth()->user()->id,

        ];
    }
}
