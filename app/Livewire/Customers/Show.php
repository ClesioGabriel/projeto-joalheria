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

    public function closeModal()
    {
        $this->showViewModal = false;   // FECHA O MODAL
    }

    public function render()
    {
        return view('livewire.customers.show');
    }
}
