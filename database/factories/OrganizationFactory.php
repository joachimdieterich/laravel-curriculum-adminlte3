<?php

namespace Database\Factories;

use App\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrganizationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Organization::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->company,
            'description' => $this->faker->sentence,

            'street' => $this->faker->streetAddress,
            'postcode' => $this->faker->postcode,
            'city' => $this->faker->city,

            'state_id' => 'DE-RP',
            'country_id' => 'DE',
            'organization_type_id' => 1,

            'phone' => $this->faker->phoneNumber,
            'email' => $this->faker->email,

            'status_id' => 1,
        ];
    }
}
