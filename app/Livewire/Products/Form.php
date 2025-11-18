<?php

namespace App\Livewire\Products;

use App\Models\Product; // <-- Importar o Model
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;

class Form extends Component
{
    use WithFileUploads;

    public $name;
    public $price;
    public $description;
    public ?Product $product = null;

    public $metal;
    public $weight;
    public $stone_type;
    public $stone_size;
    public $photo;
    public $location;
    public $serial_number;
    public $existing_photo_path;

    public $stock = 0; 
    public $product_type = Product::TYPE_FINISHED;

    public function mount(?Product $product = null)
    {
        if ($product && $product->exists) {
            $this->product = $product;
            $this->name = $product->name;
            $this->price = $product->price;
            $this->description = $product->description;
            $this->metal = $product->metal;
            $this->weight = $product->weight;
            $this->stone_type = $product->stone_type;
            $this->stone_size = $product->stone_size;
            $this->location = $product->location;
            $this->serial_number = $product->serial_number;
            $this->existing_photo_path = $product->photo_path;

            $this->stock = $product->stock;
            $this->product_type = $product->product_type;
        }
    }

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'metal' => 'nullable|string|max:255',
            'weight' => 'nullable|numeric|min:0',
            'stone_type' => 'nullable|string|max:255',
            'stone_size' => 'nullable|string|max:255',
            'photo' => 'nullable|image|max:1024',
            'location' => 'nullable|string|max:255',

            'stock' => 'required|integer|min:0',
            'product_type' => 'required|string|in:' . Product::TYPE_FINISHED . ',' . Product::TYPE_RAW,
        ];
    }

    public function save()
    {
        $this->validate();

        $photoPath = $this->existing_photo_path;
        if ($this->photo) {
            $photoPath = $this->photo->store('product-photos', 'public');
        }

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
            
            'stock' => $this->stock,
            'product_type' => $this->product_type,
        ];

        if ($this->product && $this->product->exists) {
            $this->product->update($data);
        } else {
            $data['serial_number'] = 'SN-' . now()->format('Ymd') . '-' . Str::random(6);
            while (Product::where('serial_number', $data['serial_number'])->exists()) {
                $data['serial_number'] = 'SN-' . now()->format('Ymd') . '-' . Str::random(7);
            }
            Product::create($data);
        }

        session()->flash('success', 'Produto salvo com sucesso!');
        $this->dispatch('product-saved');
    }

    #[On('set-product')]
    public function setProduct(Product $product)
    {
        $this->product = $product;
        $this->name = $product->name;
        $this->price = $product->price;
        $this->description = $product->description;
        $this->metal = $product->metal;
        $this->weight = $product->weight;
        $this->stone_type = $product->stone_type;
        $this->stone_size = $product->stone_size;
        $this->location = $product->location;
        $this->serial_number = $product->serial_number;
        $this->existing_photo_path = $product->photo_path;
        $this->photo = null;

        $this->stock = $product->stock;
        $this->product_type = $product->product_type;
    }

    public function render()
    {
        return view('livewire.products.form');
    }
}