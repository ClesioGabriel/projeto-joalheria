<?php

namespace App\Livewire\Products;

use App\Models\Product;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Storage;

#[Layout('layouts.app')]
class Index extends Component
{
    use WithFileUploads;

    public bool $showFormModal = false;
    public bool $showViewModal = false;
    public ?Product $selectedProduct = null;

    public string $search = '';

    #[On('product-saved')]
    #[On('close-form-modal')]
    public function closeFormModal(): void
    {
        $this->showFormModal = false;
        $this->selectedProduct = null;
        $this->resetPage();
    }

    public function create(): void
    {
        $this->selectedProduct = null;
        $this->showFormModal = true;
        $this->dispatch('set-product', null);
    }

    public function edit(Product $product): void
    {
        $this->selectedProduct = $product;
        $this->showFormModal = true;
        $this->dispatch('set-product', $product);
    }

    public function view(Product $product): void
    {
        $this->selectedProduct = $product; // manter compatibilidade; product já tem campos necessários
        $this->showViewModal = true;
    }

    public function delete(Product $product): void
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();
        $this->dispatchBrowserEvent('notify', ['message' => 'Produto excluído com sucesso!']);
        $this->resetPage();
    }

    public function render()
    {
        $query = Product::query();

        if ($this->search !== '') {
            $query->where('name', 'like', "%{$this->search}%")
                ->orWhere('description', 'like', "%{$this->search}%");
        }

        return view('livewire.products.index', [
            'products' => $query->latest()->paginate(10),
            'productTypes' => Product::types(),
        ]);
    }
}
