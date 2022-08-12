<?php

namespace Database\Factories;

use App\Grade;
use Illuminate\Database\Eloquent\Factories\Factory;

class GradeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Grade::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $begin = $this->faker->numberBetween(1, 13);
        $end = $this->faker->numberBetween($begin, 13);

        return [
            'title' => "Grade {$begin}/{$end}",
            'external_begin' => $begin,
            'external_end' => $end,
            'organization_type_id' => $this->faker->numberBetween(1, 10), //get random organization_type_id of seeded types
        ];
    }
}
