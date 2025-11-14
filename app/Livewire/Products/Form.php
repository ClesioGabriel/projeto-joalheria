<?php

namespace App\Livewire\Products;

use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\On;

class Form extends Component
{
    use WithFileUploads;

    public ?Product $product = null;

    public string $name = '';
    public $price = null;
    public ?string $description = null;
    public int $stock = 0;
    public ?string $type = null;
    public $image = null; // UploadedFile ou null

    public function mount(?Product $product = null)
    {
        $this->setProduct($product);
    }

    protected function rules(): array
    {
        // pegar regras do model e adaptar image caso seja upload
        $rules = Product::rules($this->product->id ?? null);

        if ($this->image) {
            $rules['image'] = 'nullable|image|max:2048';
        } else {
            // manter a regra do model (string) para o caso do campo armazenado
            $rules['image'] = $rules['image'] ?? 'nullable|string|max:255';
        }

        return $rules;
    }

    public function save(): void
    {
        $this->validate($this->rules());

        if ($this->product && $this->product->exists) {
            $this->product->update($this->payload());

            if ($this->image) {
                if ($this->product->image) {
                    Storage::disk('public')->delete($this->product->image);
                }
                $this->product->image = $this->image->store('products', 'public');
                $this->product->save();
            }
        } else {
            $path = $this->image ? $this->image->store('products', 'public') : null;
            Product::create(array_merge($this->payload(), ['image' => $path]));
        }

        $this->dispatchBrowserEvent('notify', ['message' => 'Produto salvo com sucesso!']);
        $this->dispatch('product-saved');

        $this->resetForm();
    }

    protected function payload(): array
    {
        return [
            'name' => $this->name,
            'price' => $this->price,
            'description' => $this->description,
            'stock' => $this->stock,
            'type' => $this->type ?? Product::typesKeys()[0] ?? 'produto_final',
        ];
    }

    public function resetForm(): void
    {
        $this->name = '';
        $this->price = null;
        $this->description = null;
        $this->stock = 0;
        $this->type = null;
        $this->image = null;
        $this->product = null;
    }

    #[On('set-product')]
    public function setProduct(?Product $product): void
    {
        if ($product) {
            $this->product = $product;
            $this->name = $product->name;
            $this->price = $product->price;
            $this->description = $product->description;
            $this->stock = $product->stock ?? 0;
            $this->type = $product->type ?? Product::typesKeys()[0] ?? 'produto_final';
            $this->image = null; // não sobrescrever o caminho atual; upload só quando usuário enviar
        } else {
            $this->resetForm();
            $this->type = Product::typesKeys()[0] ?? 'produto_final';
        }
    }

    public function render()
    {
        return view('livewire.products.form', [
            'productTypes' => Product::types(),
        ]);
    }
}
