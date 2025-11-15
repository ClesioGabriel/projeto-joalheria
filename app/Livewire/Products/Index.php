<?php

namespace App\Livewire\Products;

use App\Models\Product;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
<<<<<<< HEAD
use Livewire\WithPagination; // Importar paginação se não estiver no seu original
=======
use Illuminate\Support\Facades\Storage;
>>>>>>> origin/feat/arthur

#[Layout('layouts.app')]
class Index extends Component
{
<<<<<<< HEAD
    use WithPagination; // Usar a trait de paginação
=======
    use WithFileUploads;
>>>>>>> origin/feat/arthur

    public bool $showFormModal = false;
    public bool $showViewModal = false;
    public ?Product $selectedProduct = null;

<<<<<<< HEAD
    // As propriedades $name, $price, e $description foram removidas
    // pois o componente 'Form' agora gerencia seu próprio estado.
=======
    public string $search = '';
>>>>>>> origin/feat/arthur

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
<<<<<<< HEAD
        // $this->resetFormFields(); // Removido, não é mais necessário
        $this->showFormModal = true;

        // $this->showDropdown = false; // Removido (parecia ser código antigo)
=======
        $this->showFormModal = true;
        $this->dispatch('set-product', null);
>>>>>>> origin/feat/arthur
    }

    public function edit(Product $product): void
    {
<<<<<<< HEAD
        // Os campos $name, $price, etc., não precisam ser definidos aqui.
        // O 'Form.php' cuida disso no 'mount()'.

        $this->selectedProduct = $product;
        $this->showFormModal = true;

        // $this->showDropdown = false; // Removido
=======
        $this->selectedProduct = $product;
        $this->showFormModal = true;
        $this->dispatch('set-product', $product);
>>>>>>> origin/feat/arthur
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

<<<<<<< HEAD
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
=======
        $product->delete();
        $this->dispatchBrowserEvent('notify', ['message' => 'Produto excluído com sucesso!']);
        $this->resetPage();
    }
>>>>>>> origin/feat/arthur

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