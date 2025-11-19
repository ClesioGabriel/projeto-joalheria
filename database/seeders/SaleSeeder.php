<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Sale;
use App\Models\Customer;
use App\Models\Product;

class SaleSeeder extends Seeder
{
    public function run(): void
    {
        $customers = Customer::all();
        $products = Product::all();

        // pega apenas as chaves dos status: ['processando', 'em_producao', ...]
        $statuses = array_keys(Sale::statuses());

        for ($i = 0; $i < 5; $i++) {
            $customer = $customers->random();

            // sorteia de 1 a 3 produtos
            $count = $products->count();
            $numProducts = rand(1, min(3, $count));
            $selectedProducts = $products->random($numProducts);

            $total = 0;

            // cria a venda já com um status válido
            $sale = Sale::create([
                'customer_id'   => $customer->id,
                'date'          => now(),
                'date_finish'   => now()->addWeek(),
                'total_amount'  => 0, // atualizado depois
                'status'        => $statuses[array_rand($statuses)], // 👈 status aleatório válido
            ]);

            // cria os itens da venda
            foreach ($selectedProducts as $product) {
                $quantity   = rand(1, 5);
                $unit_price = $product->price;
                $subtotal   = $quantity * $unit_price;

                $sale->items()->create([
                    'product_id' => $product->id,
                    'quantity'   => $quantity,
                    'unit_price' => $unit_price,
                    'subtotal'   => $subtotal,
                ]);

                $total += $subtotal;
            }

            // atualiza o total da venda
            $sale->update(['total_amount' => $total]);
        }
    }
}
