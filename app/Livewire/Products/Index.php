<?php

namespace App\Livewire\Products;

use App\Models\Product;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\WithPagination; // Importar paginação se não estiver no seu original

#[Layout('layouts.app')]
class Index extends Component
{
    use WithPagination; // Usar a trait de paginação

    public bool $showFormModal = false;
    public bool $showViewModal = false;
    public ?Product $selectedProduct = null;

    // As propriedades $name, $price, e $description foram removidas
    // pois o componente 'Form' agora gerencia seu próprio estado.

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
        // $this->resetFormFields(); // Removido, não é mais necessário
        $this->showFormModal = true;

        // $this->showDropdown = false; // Removido (parecia ser código antigo)
    }

    public function edit(Product $product)
    {
        // Os campos $name, $price, etc., não precisam ser definidos aqui.
        // O 'Form.php' cuida disso no 'mount()'.

        $this->selectedProduct = $product;
        $this->showFormModal = true;

        // $this->showDropdown = false; // Removido
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
        // Adicionar lógica para deletar a foto se ela existir
        if ($product->photo_path) {
            \Storage::disk('public')->delete($product->photo_path);
        }
        $product->delete();
        $this->dispatch('notify', 'Produto excluído com sucesso!');
    }

    // A função 'resetFormFields()' foi removida por ser desnecessária.

    public function render()
    {
        return view('livewire.products.index', [
            'products' => Product::latest()->paginate(10),
        ]);
    }
}