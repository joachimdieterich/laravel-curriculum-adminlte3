<?php


namespace Database\Factories;

use App\Period;
use Illuminate\Database\Eloquent\Factories\Factory;

class PeriodFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Period::class;

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

        //dd($start_date->format('Y-m-d H:i:s'));

        return [
            'title' => $start_date->format('Y').'-'.$end_date->format('Y'),
            'begin' => $start_date_string,
            'end' => $end_date_string,
            'owner_id' => auth()->user()->id,

        ];
    }

}
