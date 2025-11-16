<?php

namespace App\Livewire\Addresses;

use App\Models\Address;
use Livewire\Component;
use Livewire\Attributes\On;

class Show extends Component
{
    public Address $address;

    public function mount(Address $address)
    {
        // load customers relation for display
        $this->address = $address->load('customers');
    }

    public function render()
    {
        return view('livewire.addresses.show');
    }

    public function closeModal()
    {
        $this->emitUp('close-view-modal');
    }
}
