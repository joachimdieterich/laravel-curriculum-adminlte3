<?php

namespace Database\Factories;

use App\Certificate;
use Illuminate\Database\Eloquent\Factories\Factory;

class CertificateFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Certificate::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->company,
            'description' => $this->faker->paragraph,
            'body' => $this->faker->paragraph,
            'curriculum_id' => 1,
            'organization_id' => 1,
            'owner_id' => auth()->user()->id,
        ];
    }
}
