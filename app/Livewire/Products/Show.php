<?php

namespace App\Livewire\Products;

use App\Models\Product;
use Livewire\Component;
use Livewire\Attributes\On;

class Show extends Component
{
    public Product $product;

    protected $listeners = ['closeViewModal' => 'closeModal'];

    public function mount(Product $product)
    {
        $this->product = $product;
    }

    public function closeModal(): void
    {
        $this->dispatch('close-view-modal');
    }

    public function render()
    {
        return view('livewire.products.show');
    }
}
