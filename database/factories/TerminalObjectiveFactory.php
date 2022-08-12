<?php
namespace Database\Factories;

use App\TerminalObjective;
use Illuminate\Database\Eloquent\Factories\Factory;

class TerminalObjectiveFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TerminalObjective::class;


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
            'curriculum_id' => 1,
            'objective_type_id' => 1,
        ];
    }

}
