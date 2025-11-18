<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Product;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        $productTypes = [
            Product::TYPE_FINISHED,
            Product::TYPE_RAW,
        ];

        $metals = ['ouro', 'prata', 'bronze', 'aço', null];

        $stones = [
            'diamante',
            'esmeralda',
            'rubi',
            'ametista',
            null
        ];

        return [
            'name' => ucfirst($this->faker->word()),
            'product_type' => $this->faker->randomElement($productTypes),

            'price' => $this->faker->randomFloat(2, 50, 8000),
            'stock' => $this->faker->numberBetween(0, 50),

            'description' => $this->faker->sentence(),

            'image' => null,
            'photo_path' => 'product-photos/' . $this->faker->uuid() . '.jpg',

            'metal' => $this->faker->randomElement($metals),
            'weight' => $this->faker->randomFloat(2, 1, 50),
            'stone_type' => $this->faker->randomElement($stones),
            'stone_size' => $this->faker->randomElement(['1mm', '2mm', '3mm', '5mm', null]),

            'serial_number' => strtoupper($this->faker->bothify('SN-####??')),
            'location' => $this->faker->randomElement(['Vitrine', 'Cofre', 'Estoque', null]),
        ];
    }
}
