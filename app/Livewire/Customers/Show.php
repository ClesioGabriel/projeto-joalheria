<?php

namespace App\Livewire\Customers;

use App\Models\Customer;
use Livewire\Component;

class Show extends Component
{
    public Customer $customer;
    public bool $showViewModal = true;

    public function mount(Customer $customer)
    {
        $this->customer = $customer->load('addresses');
    }

    // mantido caso queira dispatch de evento
    public function closeModal()
    {
        $this->dispatch('close-view-modal');
    }

    public function render()
    {
        return view('livewire.customers.show');
    }
}
