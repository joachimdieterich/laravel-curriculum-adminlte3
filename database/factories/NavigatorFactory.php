<?php

namespace Database\Factories;

use App\Navigator;
use Illuminate\Database\Eloquent\Factories\Factory;

class NavigatorFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Navigator::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->title,
            'organization_id' => 1,
        ];
    }
}
