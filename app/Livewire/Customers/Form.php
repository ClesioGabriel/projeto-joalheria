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

    public array $addresses = [];
    public array $removedAddressIds = [];

    public function mount(?Customer $customer = null)
    {
        $this->setCustomer($customer);
    }

    public function addAddressRow(): void
    {
        $this->addresses[] = [
            'id' => null,
            'street' => '',
            'number' => '',
            'neighborhood' => '',
            'city' => '',
            'state' => '',
            'cep' => '',
        ];
    }

    public function removeAddressRow(int $index): void
    {
        if (!isset($this->addresses[$index])) {
            return;
        }

        $id = $this->addresses[$index]['id'] ?? null;
        if ($id) {
            $this->removedAddressIds[] = $id;
        }

        array_splice($this->addresses, $index, 1);

        if (empty($this->addresses)) {
            $this->addAddressRow();
        }
    }

    public function save(): void
    {
        $this->validate(
            Customer::rulesWithAddresses($this->customer->id ?? null),
            Customer::messages()
        );

        DB::transaction(function () {
            $this->customer = $this->customer && $this->customer->exists
                ? tap($this->customer)->update($this->customerPayload())
                : Customer::create($this->customerPayload());

            $keepIds = [];

            foreach ($this->addresses as $addr) {
                $hasAny = collect(['street', 'number', 'neighborhood', 'city', 'state', 'cep'])
                    ->contains(fn($f) => !empty($addr[$f]));

                if (!$hasAny) continue;

                if (!empty($addr['id'])) {
                    $address = Address::where('id', $addr['id'])
                        ->where('customer_id', $this->customer->id)
                        ->first();

                    if ($address) {
                        $address->update($this->addressPayload($addr));
                        $keepIds[] = $address->id;
                        continue;
                    }
                }

                $new = Address::create(array_merge(
                    $this->addressPayload($addr),
                    ['customer_id' => $this->customer->id]
                ));
                $keepIds[] = $new->id;
            }

            if (!empty($this->removedAddressIds)) {
                Address::whereIn('id', $this->removedAddressIds)
                    ->where('customer_id', $this->customer->id)
                    ->delete();
            }

            Address::where('customer_id', $this->customer->id)
                ->whereNotIn('id', $keepIds)
                ->delete();
        });

        $this->dispatchBrowserEvent('notify', ['message' => 'Cliente salvo com sucesso!']);
        $this->dispatch('customer-saved'); // 🔥 trocado emit → dispatch
        $this->resetFormState();
    }

    protected function customerPayload(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
        ];
    }

    protected function addressPayload(array $addr): array
    {
        return [
            'street' => $addr['street'] ?? null,
            'number' => $addr['number'] ?? null,
            'neighborhood' => $addr['neighborhood'] ?? null,
            'city' => $addr['city'] ?? null,
            'state' => $addr['state'] ?? null,
            'cep' => $addr['cep'] ?? null,
        ];
    }

    #[On('set-customer')]
    public function setCustomer(?Customer $customer): void
    {
        if ($customer) {
            $this->customer = $customer;
            $this->name = $customer->name;
            $this->email = $customer->email;
            $this->phone = $customer->phone;

            $this->addresses = $customer->addresses->map(fn(Address $a) => [
                'id' => $a->id,
                'street' => $a->street,
                'number' => $a->number,
                'neighborhood' => $a->neighborhood,
                'city' => $a->city,
                'state' => $a->state,
                'cep' => $a->cep,
            ])->toArray();

            if (empty($this->addresses)) {
                $this->addAddressRow();
            }
        } else {
            $this->customer = null;
            $this->name = '';
            $this->email = '';
            $this->phone = null;
            $this->addresses = [[
                'id' => null,
                'street' => '',
                'number' => '',
                'neighborhood' => '',
                'city' => '',
                'state' => '',
                'cep' => '',
            ]];
            $this->removedAddressIds = [];
        }
    }

    protected function resetFormState(): void
    {
        $this->addresses = [[
            'id' => null,
            'street' => '',
            'number' => '',
            'neighborhood' => '',
            'city' => '',
            'state' => '',
            'cep' => '',
        ]];
        $this->removedAddressIds = [];
    }

    public function render()
    {
        return view('livewire.customers.form');
    }
}
