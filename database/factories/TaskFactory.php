<?php

namespace Database\Factories;

use App\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Task::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $start_date = $this->faker->dateTimeThisYear();
        $start_date_clone = clone $start_date;
        $end_date = $this->faker->dateTimeBetween($start_date, $start_date_clone->modify('+1 year'));

        $start_date_string = $start_date->format('Y-m-d H:i:s');
        $end_date_string = $end_date->format('Y-m-d H:i:s');

        return [
            'title' => $this->faker->company,
            'description' => $this->faker->word,
            'start_date' => $start_date_string,
            'due_date' => $end_date_string,
            'owner_id' => auth()->user()->id,
        ];
    }
}
