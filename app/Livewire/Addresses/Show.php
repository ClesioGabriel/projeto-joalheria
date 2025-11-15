<?php

namespace App\Livewire\Addresses;

use App\Models\Address;
use Livewire\Component;
use Livewire\Attributes\On;

class Show extends Component
{
    public Address $address;

    #[On('close-address-view')]
    public function closeModal(): void
    {
        $this->dispatch('close-address-view');
    }

    public function mount(Address $address)
    {
        $this->address = $address->load('customer');
    }

    public function render()
    {
        return view('livewire.addresses.show');
    }
}
