<?php

namespace App\Livewire\Addresses;

use App\Models\Address;
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

    #[On('set-address')]
    public function setAddress(?Address $address): void
    {
        if ($address) {
            $this->address = $address;
            $this->customer_id = $address->customer_id;
            $this->street = $address->street ?? '';
            $this->number = $address->number ?? null;
            $this->neighborhood = $address->neighborhood ?? null;
            $this->city = $address->city ?? null;
            $this->state = $address->state ?? null;
            $this->cep = $address->cep ?? null;
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
        // usa o setAddress para inicializar corretamente
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

        if ($this->address && $this->address->exists) {
            $this->address->update($this->payload());
            $this->dispatch('notify', ['message' => 'Endereço atualizado!']);
        } else {
            $data = $this->payload();
            if (! $this->customer_id) {
                $this->dispatch('notify', ['message' => 'Cliente não informado para o endereço.', 'type' => 'error']);
                return;
            }
            $data['customer_id'] = $this->customer_id;
            Address::create($data);
            $this->dispatch('notify', ['message' => 'Endereço criado!']);
        }

        // evento para outros componentes/listeners
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
        // reseta campos do formulário
        $this->reset(['street', 'number', 'neighborhood', 'city', 'state', 'cep', 'address', 'customer_id']);
    }

    public function render()
    {
        return view('livewire.addresses.form');
    }
}
