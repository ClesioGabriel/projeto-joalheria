<?php

namespace App\Livewire\Addresses;

use App\Models\Address;
use App\Models\Customer;
use Livewire\Component;
use Livewire\Attributes\On;

class Form extends Component
{
    public ?Address $address = null;

    public ?int $customer_id = null;

    public string $street = '';
    public ?string $number = null;
    public ?string $neighborhood = null;
    public ?string $city = null;
    public ?string $state = null;
    public ?string $cep = null;

    public int $ownersCount = 0;

    #[On('set-address')]
    public function setAddress(?Address $address): void
    {
        if ($address) {
            $this->address = $address->fresh('customers');
            $firstCustomer = $this->address->customers->first();
            $this->customer_id = $firstCustomer->id ?? null;

            $this->street = $this->address->street ?? '';
            $this->number = $this->address->number ?? null;
            $this->neighborhood = $this->address->neighborhood ?? null;
            $this->city = $this->address->city ?? null;
            $this->state = $this->address->state ?? null;
            $this->cep = $this->address->cep ?? null;

            $this->ownersCount = $this->address->customers()->count();
        } else {
            $this->resetForm();
        }
    }

    #[On('set-customer-for-address')]
    public function setCustomer(int $customerId): void
    {
        $this->customer_id = $customerId;
    }

    public function mount(?Address $address = null, ?int $customerId = null)
    {
        $this->setAddress($address);
        if ($customerId) {
            $this->customer_id = $customerId;
        }
    }

    public function rules(): array
    {
        return Address::rules();
    }

    public function save(): void
    {
        $this->validate();

        if (! $this->address && ! $this->customer_id) {
            $this->dispatch('notify', ['message' => 'Informe um cliente para associar o endereço.', 'type' => 'error']);
            return;
        }

        $payload = $this->payload();

        if ($this->address && $this->address->exists) {
            $owners = $this->address->customers()->count();

            if ($owners > 1) {
                $new = Address::create($payload);

                if ($this->customer_id) {
                    $customer = Customer::find($this->customer_id);
                    if ($customer) {
                        $customer->addresses()->attach($new->id);
                    }
                }

                if ($this->customer_id) {
                    $this->address->customers()->detach($this->customer_id);
                }

                $this->dispatch('notify', ['message' => 'Endereço duplicado e associado ao cliente (não sobrescrevemos endereço compartilhado).', 'type' => 'success']);
            } else {
                $this->address->update($payload);
                $this->dispatch('notify', ['message' => 'Endereço atualizado!', 'type' => 'success']);

                if ($this->customer_id && ! $this->address->customers()->where('id', $this->customer_id)->exists()) {
                    $customer = Customer::find($this->customer_id);
                    if ($customer) {
                        $customer->addresses()->attach($this->address->id);
                    }
                }
            }
        } else {
            $new = Address::create($payload);
            if ($this->customer_id) {
                $customer = Customer::find($this->customer_id);
                if ($customer) {
                    $customer->addresses()->attach($new->id);
                }
            }

            $this->dispatch('notify', ['message' => 'Endereço criado e associado!', 'type' => 'success']);
        }

        $this->dispatch('address-saved');
        $this->resetForm();
    }

    protected function payload(): array
    {
        return [
            'street' => $this->street ?: null,
            'number' => $this->number ?: null,
            'neighborhood' => $this->neighborhood ?: null,
            'city' => $this->city ?: null,
            'state' => $this->state ?: null,
            'cep' => $this->cep ?: null,
        ];
    }

    protected function resetForm(): void
    {
        $this->reset(['street', 'number', 'neighborhood', 'city', 'state', 'cep', 'address', 'customer_id', 'ownersCount']);
    }

    public function render()
    {
        return view('livewire.addresses.form');
    }
}
