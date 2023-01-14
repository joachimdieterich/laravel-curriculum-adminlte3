<?php

namespace Database\Factories;

use App\EnablingObjective;
use App\TerminalObjective;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

class EnablingObjectiveFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = EnablingObjective::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->text,
            'description' => $this->faker->sentence,
            'time_approach' => null,
            'curriculum_id' => 1,
            'terminal_objective_id' => TerminalObjective::factory()->create()->id, //DB::table('terminal_objectives')->latest()->first()->id,
            'order_id' => 0,
            'level_id' => null,
        ];
    }
}
