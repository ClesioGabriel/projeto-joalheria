<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;
use App\Models\Address;

class AddressSeeder extends Seeder
{
    public function run(): void
    {
        // Se já existir customer, cria 1 ou 2 endereços para cada
        Customer::all()->each(function (Customer $customer) {
            Address::factory()
                ->count(rand(1, 2))
                ->create(['customer_id' => $customer->id]);
        });

        // Caso não haja customers cadastrados
        if (Customer::count() === 0) {
            Address::factory()->count(10)->create();
        }
    }
}
