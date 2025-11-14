<?php

namespace App\Livewire\Customers;

use App\Models\Customer;
use Livewire\Component;

class Show extends Component
{
    public Customer $customer;
    public bool $showViewModal = true; // 🔥 agora o modal tem um estado próprio

    protected $listeners = ['close-view-modal' => 'closeModal'];

    public function mount(Customer $customer)
    {
        $this->customer = $customer->load('addresses');
    }

    public function closeModal(): void
    {
        $this->dispatch('close-view-modal'); // avisa o pai pra fechar
    }

    public function render()
    {
        return view('livewire.customers.show');
    }
}
