<?php

namespace App\Livewire\Sales;

use Livewire\Component;
use App\Models\Sale;
use Livewire\Attributes\On;

class Show extends Component
{
    public Sale $sale;

    protected $listeners = ['closeViewModal' => 'closeModal'];

    public function mount(Sale $sale)
    {
        $this->sale = $sale->load('items.product', 'customer');
    }

    public function closeModal(): void
    {
        $this->dispatch('close-view-modal');
    }

    public function render()
    {
        return view('livewire.sales.show');
    }
}
