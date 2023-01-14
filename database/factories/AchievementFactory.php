<?php

namespace Database\Factories;

use App\Achievement;
use Illuminate\Database\Eloquent\Factories\Factory;

class AchievementFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Achievement::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'referenceable_type' => 'App\EnablingObjective',
            'referenceable_id' => 1,
            'status' => '1',
        ];
    }
}
