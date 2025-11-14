<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        Product::create([
            'name' => 'Anel de ouro',
            'price' => 799.90,
            'description' => 'Anel de ouro'
        ]);

        Product::create([
            'name' => 'Corrente de ouro',
            'price' => 3599.00,
            'description' => 'Corrente'
        ]);

        Product::create([
            'name' => 'Brinco de diamante',
            'price' => 299.00,
            'description' => 'Brinco de diamante'
        ]);

        Product::create([
            'name' => 'Colar de prata',
            'price' => 159.90,
            'description' => 'Colar de prata'
        ]);

        Product::create([
            'name' => 'Pulseira de prata banhada a ouro"',
            'price' => 1299.00,
            'description' => 'Pulseira banhada a ouro'
        ]);
    }
}
