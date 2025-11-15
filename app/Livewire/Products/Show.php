<?php

namespace App\Livewire\Products;

use App\Models\Product;
use Livewire\Component;
use Livewire\Attributes\On; // Importar On

class Show extends Component
{

    public Product $product;

    public function closeModal()
    {
        $this->dispatch('close-view-modal');
    }


    public function render()
    {
        return view('livewire.products.show');
    }
}