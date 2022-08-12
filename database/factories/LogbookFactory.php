<?php

namespace Database\Factories;

use App\Logbook;
use Illuminate\Database\Eloquent\Factories\Factory;

class LogbookFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Logbook::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->word,
            'description' => $this->faker->paragraph,

            'owner_id' => auth()->user()->id,
        ];
    }
}
