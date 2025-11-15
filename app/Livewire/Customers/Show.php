<?php

namespace App\Livewire\Customers;

use App\Models\Customer;
use Livewire\Component;

class Show extends Component
{
    public Customer $customer;
    public bool $showViewModal = true;

    protected $listeners = ['close-view-modal' => 'closeModal'];

    public function mount(Customer $customer)
    {
        $this->customer = $customer->load('addresses');
    }

    public function closeModal()
    {
        // notifica o Index que deve fechar o modal
        $this->dispatch('close-view-modal');
        // e aciona também um browser event para fechar se você usar Alpine
        $this->dispatchBrowserEvent('close-view-modal');
    }

    public function render()
    {
        return view('livewire.customers.show');
    }
}
