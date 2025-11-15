<?php

namespace App\Livewire\Products;

use App\Models\Product;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\WithFileUploads; // <-- Importar para upload de fotos
use Illuminate\Support\Str;    // <-- Importar para gerar serial

class Form extends Component
{
    use WithFileUploads; // <-- Usar a trait

    // Propriedades existentes
    public $name;
    public $price;
    public $description;
    public ?Product $product = null;

    // --- ADICIONAR NOVAS PROPRIEDADES ---
    public $metal;
    public $weight;
    public $stone_type;
    public $stone_size;
    public $photo; // Para o upload
    public $location;
    public $serial_number; // Será gerado, mas pode ser exibido
    public $existing_photo_path; // Para exibir a foto atual
    // ------------------------------------

    public function mount(?Product $product = null)
    {
        if ($product && $product->exists) { // Corrigido para checar $product->exists
            $this->product = $product;
            $this->name = $product->name;
            $this->price = $product->price;
            $this->description = $product->description;
            // --- ADICIONAR NOVOS CAMPOS NO MOUNT ---
            $this->metal = $product->metal;
            $this->weight = $product->weight;
            $this->stone_type = $product->stone_type;
            $this->stone_size = $product->stone_size;
            $this->location = $product->location;
            $this->serial_number = $product->serial_number;
            $this->existing_photo_path = $product->photo_path;
            // ---------------------------------------
        }
    }

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            // --- ADICIONAR NOVAS REGRAS ---
            'metal' => 'nullable|string|max:255',
            'weight' => 'nullable|numeric|min:0',
            'stone_type' => 'nullable|string|max:255',
            'stone_size' => 'nullable|string|max:255',
            'photo' => 'nullable|image|max:1024', // Ex: 1MB max
            'location' => 'nullable|string|max:255',
            // ------------------------------
        ];
    }

    public function save()
    {
        $this->validate();

        // Tratar upload da foto
        $photoPath = $this->existing_photo_path; // Mantém a foto antiga por padrão
        if ($this->photo) {
            // Salva a nova foto em 'storage/app/public/product-photos'
            // Lembre-se de rodar 'php artisan storage:link'
            $photoPath = $this->photo->store('product-photos', 'public');
        }

        // Preparar dados
        $data = [
            'name' => $this->name,
            'price' => $this->price,
            'description' => $this->description,
            'metal' => $this->metal,
            'weight' => $this->weight,
            'stone_type' => $this->stone_type,
            'stone_size' => $this->stone_size,
            'photo_path' => $photoPath,
            'location' => $this->location,
        ];

        if ($this->product && $this->product->exists) {
            // Atualizar produto existente
            $this->product->update($data);
        } else {
            // Criar novo produto
            // Gerar número de série único
            $data['serial_number'] = 'SN-' . now()->format('Ymd') . '-' . Str::random(6);
            // Garantir que seja único (embora a chance de colisão seja baixa)
            while (Product::where('serial_number', $data['serial_number'])->exists()) {
                $data['serial_number'] = 'SN-' . now()->format('Ymd') . '-' . Str::random(7);
            }
            
            Product::create($data);
        }

        session()->flash('success', 'Produto salvo com sucesso!');
        $this->dispatch('product-saved');
        // Você pode querer fechar o modal também
        // $this->dispatch('close-form'); // Descomente se quiser fechar após salvar
    }

    #[On('set-product')]
    public function setProduct(Product $product)
    {
        $this->product = $product;
        $this->name = $product->name;
        $this->price = $product->price;
        $this->description = $product->description;
        // --- ADICIONAR NOVOS CAMPOS NO SET ---
        $this->metal = $product->metal;
        $this->weight = $product->weight;
        $this->stone_type = $product->stone_type;
        $this->stone_size = $product->stone_size;
        $this->location = $product->location;
        $this->serial_number = $product->serial_number;
        $this->existing_photo_path = $product->photo_path;
        $this->photo = null; // Limpar upload anterior
        // ------------------------------------
    }

    public function render()
    {
        return view('livewire.products.form');
    }
}