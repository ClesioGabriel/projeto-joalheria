<?php

namespace App\Livewire\Customers;

use App\Models\Customer;
use App\Models\Address;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\Attributes\On;

class Form extends Component
{
    public ?Customer $customer = null;

    public ?string $name = null;
    public ?string $email = null;
    public ?string $phone = null;

    // endereço simples (um endereço por cliente neste formulário)
    public ?string $street = null;
    public ?string $number = null;
    public ?string $neighborhood = null;
    public ?string $city = null;
    public ?string $state = null;
    public ?string $cep = null;

    #[On('set-customer')]
    public function setCustomer(?Customer $customer): void
    {
        if ($customer && $customer->exists) {
            $this->customer = $customer;
            $this->name = $customer->name;
            $this->email = $customer->email;
            $this->phone = $customer->phone;

            // pega o primeiro endereço se houver
            $addr = $customer->addresses()->first();
            $this->street = $addr->street ?? null;
            $this->number = $addr->number ?? null;
            $this->neighborhood = $addr->neighborhood ?? null;
            $this->city = $addr->city ?? null;
            $this->state = $addr->state ?? null;
            $this->cep = $addr->cep ?? null;
        } else {
            $this->customer = new Customer();
            $this->name = '';
            $this->email = '';
            $this->phone = '';
            $this->street = $this->number = $this->neighborhood = $this->city = $this->state = $this->cep = null;
        }
    }

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email,' . ($this->customer->id ?? 'NULL'),
            'phone' => 'nullable|string|max:20',

            'street' => 'nullable|string|max:255',
            'number' => 'nullable|string|max:50',
            'neighborhood' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'cep' => 'nullable|string|max:20',
        ];
    }

    public function save(): void
    {
        $this->validate();

        DB::transaction(function () {
            // salva cliente
            $data = [
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
            ];

            if ($this->customer && $this->customer->exists) {
                $this->customer->update($data);
            } else {
                $this->customer = Customer::create($data);
            }

            // trata endereço (um endereço)
            $hasAnyAddress = collect([
                $this->street, $this->number, $this->neighborhood, $this->city, $this->state, $this->cep
            ])->filter()->isNotEmpty();

            if ($hasAnyAddress) {
                $address = $this->customer->addresses()->first();
                $addrData = [
                    'street' => $this->street,
                    'number' => $this->number,
                    'neighborhood' => $this->neighborhood,
                    'city' => $this->city,
                    'state' => $this->state,
                    'cep' => $this->cep,
                ];

                if ($address) {
                    $address->update($addrData);
                } else {
                    $this->customer->addresses()->create($addrData);
                }
            }
        });

        // notifica o Index para atualizar e fecha a modal no browser
        $this->dispatch('customer-saved');
        $this->dispatchBrowserEvent('close-form-modal');
    }

    public function render()
    {
        return view('livewire.customers.form');
    }
}
