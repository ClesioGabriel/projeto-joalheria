<?php

namespace App\Livewire\Products;

use App\Models\Product;
use Livewire\Component;
<<<<<<< HEAD
use Livewire\Attributes\On; // Importar On
=======
use Livewire\Attributes\On;
>>>>>>> origin/feat/arthur

class Show extends Component
{
    // Esta propriedade será preenchida automaticamente pelo Livewire
    // por causa do :product="$selectedProduct" na view do Index.
    public Product $product;

    // Este componente não precisa de 'mount()'.
    // A propriedade pública '$product' é preenchida por ser 'bound' (ligada)
    // à prop :product que o pai (Index) passou.

<<<<<<< HEAD
    /**
     * Função para fechar o modal.
     * Ela dispara um evento 'up' (para o componente pai)
     */
    public function closeModal()
    {
        // Dispara um evento que o 'Index.php' deve ouvir
=======
    public function mount(Product $product)
    {
        $this->product = $product;
    }

    public function closeModal(): void
    {
>>>>>>> origin/feat/arthur
        $this->dispatch('close-view-modal');
    }

    /**
     * O 'render' apenas exibe a view.
     * A variável $product já está disponível automaticamente na view.
     */
    public function render()
    {
        return view('livewire.products.show');
    }
}