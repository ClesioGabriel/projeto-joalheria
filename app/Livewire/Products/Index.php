<?php

namespace App\Livewire\Products;

use App\Models\Product;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Index extends Component
{
    use WithPagination;

    public bool $showFormModal = false;
    public bool $showViewModal = false;
    public ?Product $selectedProduct = null;

    #[On('product-saved')]
    #[On('close-form-modal')]
    public function closeFormModal()
    {
        $this->showFormModal = false;
        $this->selectedProduct = null;
    }

    public function create()
    {
        $this->selectedProduct = null;
        $this->showFormModal = true;
    }

    public function edit(Product $product)
    {
        $this->selectedProduct = $product;
        $this->showFormModal = true;
    }

    public function view(Product $product)
    {
        $this->selectedProduct = $product;
        $this->showViewModal = true;
    }

    #[On('close-view-modal')]
    public function closeViewModal()
    {
        $this->showViewModal = false;
        $this->selectedProduct = null;
    }

    public function delete(Product $product)
    {
        if ($product->photo_path) {
            \Storage::disk('public')->delete($product->photo_path);
        }

        $product->delete();

        $this->dispatch('notify', 'Produto excluído com sucesso!');
    }

    public function render()
    {
        return view('livewire.products.index', [
            'products' => Product::latest()->paginate(10),
        ]);
    }
}
