<?php

namespace Database\Factories;

use App\Medium;
use Illuminate\Database\Eloquent\Factories\Factory;

class MediumFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Medium::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [

            'path' => $this->faker->file('/tmp', '/tmp/'),
            'medium_name' => $this->faker->word.$this->faker->fileExtension,
            'title' => $this->faker->word,
            'description' => $this->faker->sentence,

            'author' => $this->faker->name,
            'publisher' => $this->faker->company,
            'city' => $this->faker->city,
            'date' => $this->faker->dateTime,

            'size' => $this->faker->numberBetween(1, 3000000).'kb',
            'mime_type' => $this->faker->mimeType,

            'license_id' => 1,
            'owner_id' => auth()->user()->id,
        ];
    }
}
