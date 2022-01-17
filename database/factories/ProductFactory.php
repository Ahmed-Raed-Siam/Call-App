<?php

namespace Database\Factories;

use App\Models\ServiceType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->unique->name,
            'description' => $this->faker->words('4', 'true'),
            'service_type_id' => ServiceType::all()->random()->id,
//            'image' => '',
            'order' => 0,
            'cost' => $this->faker->randomNumber(3),
        ];
    }
}
