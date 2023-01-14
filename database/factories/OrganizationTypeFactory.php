<?php

namespace Database\Factories;

use App\OrganizationType;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrganizationTypeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = OrganizationType::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->company,
            'external_id' => $this->faker->numberBetween(1, 100),
            'state_id' => 'DE-RP',
            'country_id' => 'DE',
        ];
    }
}
