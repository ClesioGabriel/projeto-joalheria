<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        Customer::create([
            'name'  => 'Maria Oliveira',
            'email' => 'maria@gmail.com',
            'phone' => '31999990001',
            'cpf'   => '123.456.789-01',
        ]);

        Customer::create([
            'name'  => 'João Silva',
            'email' => 'joao@gmail.com',
            'phone' => '31999990002',
            'cpf'   => '987.654.321-00',
        ]);

        Customer::create([
            'name'  => 'Genésio',
            'email' => 'genesio@gmail.com',
            'phone' => '31999990003',
            'cpf'   => '111.222.333-44',
        ]);

        Customer::create([
            'name'  => 'Luis Henrique',
            'email' => 'luishenrique@gmail.com',
            'phone' => '31999990004',
            'cpf'   => '222.333.444-55',
        ]);

        Customer::create([
            'name'  => 'Clésio',
            'email' => 'clesio@gmail.com',
            'phone' => '31999990005',
            'cpf'   => '333.444.555-66',
        ]);

        Customer::create([
            'name'  => 'Bruno',
            'email' => 'bruno@gmail.com',
            'phone' => '31999990006',
            'cpf'   => '444.555.666-77',
        ]);

        Customer::create([
            'name'  => 'Henrique',
            'email' => 'henrique@gmail.com',
            'phone' => '31999990007',
            'cpf'   => '555.666.777-88',
        ]);

        Customer::create([
            'name'  => 'Gabriel',
            'email' => 'gabriel@gmail.com',
            'phone' => '31999990008',
            'cpf'   => '666.777.888-99',
        ]);
    }
}
