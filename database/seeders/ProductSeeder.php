<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'name' => 'Anel de ouro',
                'product_type' => Product::TYPE_FINISHED,
                'price' => 799.90,
                'stock' => 10,
                'description' => 'Anel de ouro 18k.',
                'metal' => 'ouro',
                'weight' => 3.5,
                'stone_type' => 'diamante',
                'stone_size' => '2mm',
                'photo_path' => 'product-photos/anel-ouro.jpg',
                'serial_number' => 'SN-ANELOURO01',
                'location' => 'Vitrine',
            ],
            [
                'name' => 'Corrente de ouro',
                'product_type' => Product::TYPE_FINISHED,
                'price' => 3599.00,
                'stock' => 5,
                'description' => 'Corrente de ouro maciço.',
                'metal' => 'ouro',
                'weight' => 12.8,
                'stone_type' => null,
                'stone_size' => null,
                'photo_path' => 'product-photos/corrente-ouro.jpg',
                'serial_number' => 'SN-CORROURO01',
                'location' => 'Vitrine',
            ],
            [
                'name' => 'Brinco de diamante',
                'product_type' => Product::TYPE_FINISHED,
                'price' => 299.00,
                'stock' => 20,
                'description' => 'Brinco delicado com diamantes.',
                'metal' => 'prata',
                'weight' => 1.2,
                'stone_type' => 'diamante',
                'stone_size' => '1mm',
                'photo_path' => 'product-photos/brinco-diamante.jpg',
                'serial_number' => 'SN-BRINCOD01',
                'location' => 'Vitrine',
            ],
            [
                'name' => 'Colar de prata',
                'product_type' => Product::TYPE_FINISHED,
                'price' => 159.90,
                'stock' => 12,
                'description' => 'Colar simples de prata.',
                'metal' => 'prata',
                'weight' => 4.0,
                'stone_type' => null,
                'stone_size' => null,
                'photo_path' => 'product-photos/colar-prata.jpg',
                'serial_number' => 'SN-COLARPRATA01',
                'location' => 'Estoque',
            ],
            [
                'name' => 'Pulseira de prata banhada a ouro',
                'product_type' => Product::TYPE_FINISHED,
                'price' => 1299.00,
                'stock' => 7,
                'description' => 'Pulseira de prata com banho de ouro.',
                'metal' => 'prata',
                'weight' => 6.7,
                'stone_type' => null,
                'stone_size' => null,
                'photo_path' => 'product-photos/pulseira-banhada-ouro.jpg',
                'serial_number' => 'SN-PULSEIRA01',
                'location' => 'Vitrine',
            ],
            // exemplo de matéria-prima
            [
                'name' => 'Ouro bruto 24k',
                'product_type' => Product::TYPE_RAW,
                'price' => 4999.00,
                'stock' => 2,
                'description' => 'Ouro bruto para fundição.',
                'metal' => 'ouro',
                'weight' => 15.3,
                'stone_type' => null,
                'stone_size' => null,
                'photo_path' => 'product-photos/ouro-bruto.jpg',
                'serial_number' => 'SN-OUROBRUTO01',
                'location' => 'Cofre',
            ],
        ];

        foreach ($products as $data) {
            Product::create($data);
        }

        // gerar alguns produtos aleatórios com factory
        Product::factory()->count(10)->create();
    }
}
