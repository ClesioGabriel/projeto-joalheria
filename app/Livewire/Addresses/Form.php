<?php

namespace App\Livewire\Addresses;

use App\Models\Address;
use App\Models\Customer;
use Livewire\Component;
use Livewire\Attributes\On;

class Form extends Component
{
    public ?Address $address = null;

    // optional: we accept a customer_id when creating/attaching addresses
    public ?int $customer_id = null;

    public string $street = '';
    public ?string $number = null;
    public ?string $neighborhood = null;
    public ?string $city = null;
    public ?string $state = null;
    public ?string $cep = null;

    // used to show a warning if the address is shared
    public int $ownersCount = 0;

    #[On('set-address')]
    public function setAddress(?Address $address): void
    {
        if ($address) {
            $this->address = $address->fresh('customers');
            // take first customer's id as context (if any)
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
        // initialize via setAddress so ownersCount etc. are set
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

        // must have a customer context for attaching when creating a new address
        if (! $this->address && ! $this->customer_id) {
            $this->dispatch('notify', ['message' => 'Informe um cliente para associar o endereço.', 'type' => 'error']);
            return;
        }

        // payload to save
        $payload = $this->payload();

        if ($this->address && $this->address->exists) {
            $owners = $this->address->customers()->count();

            if ($owners > 1) {
                // address is shared: do not update in-place because it would affect others
                // create a new address and attach only to the provided customer (if any)
                $new = Address::create($payload);

                if ($this->customer_id) {
                    $customer = Customer::find($this->customer_id);
                    if ($customer) {
                        $customer->addresses()->attach($new->id);
                    }
                }

                // if editing context had a specific customer, detach that customer from old address and attach new
                if ($this->customer_id) {
                    $this->address->customers()->detach($this->customer_id);
                }

                $this->dispatch('notify', ['message' => 'Endereço duplicado e associado ao cliente (não sobrescrevemos endereço compartilhado).', 'type' => 'success']);
            } else {
                // safe to update in-place (address unique to this customer)
                $this->address->update($payload);
                $this->dispatch('notify', ['message' => 'Endereço atualizado!', 'type' => 'success']);

                // if there is a customer_id and the pivot is missing, attach
                if ($this->customer_id && ! $this->address->customers()->where('id', $this->customer_id)->exists()) {
                    $customer = Customer::find($this->customer_id);
                    if ($customer) {
                        $customer->addresses()->attach($this->address->id);
                    }
                }
            }
        } else {
            // create new address and attach to customer
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
