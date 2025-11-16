<?php

namespace App\Livewire\Sales;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Customer;
use App\Models\Product;
use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Collection; // Importar Collection

class Form extends Component
{
    public ?int $customer_id = null;
    public string $date;
    public array $items = [];
    public float $total_amount = 0;
    public ?Sale $sale = null;
    public ?string $status = null;

    // --- PROPRIEDADES ADICIONADAS ---
    /** @var Collection */
    public $allProducts;
    
    /** @var Collection */
    public $allCustomers;
    
    public array $allStatuses = [];
    // ---------------------------------


    public function mount(?Sale $sale = null)
    {
        // Carrega coleções auxiliares UMA VEZ
        $this->allProducts = Product::orderBy('name')->get();
        $this->allCustomers = Customer::orderBy('name')->get();
        $this->allStatuses = Sale::statuses();

        if ($sale && $sale->exists) {
            $this->sale = $sale->load('items');
            $this->customer_id = $sale->customer_id;
            $this->date = \Carbon\Carbon::parse($sale->date)->format('Y-m-d');
            $this->status = $sale->status;
            $this->items = $sale->items->map(fn($i) => [
                'product_id' => $i->product_id,
                'quantity'    => $i->quantity,
                'unit_price' => (float) $i->unit_price,
                'subtotal'   => (float) $i->subtotal,
            ])->toArray();
            $this->updateTotal();
        } else {
            $this->sale = null;
            $this->date = now()->format('Y-m-d');
            $this->items = [];
            $this->status = null; // A propriedade 'stage' não existia, 'status' sim
        }
    }

    // ... (validationRules e addItem são iguais) ...
    // ... (removeItem é igual) ...

    /**
     * Hook OTIMIZADO do Livewire v3.
     * Chamado quando um item específico do array 'items' é alterado.
     * Ex: $key = '0.product_id' ou $key = '1.quantity'
     */
    public function updatedItems($value, $key): void
    {
        $parts = explode('.', $key);

        // Garante que estamos lidando com uma chave aninhada (ex: '0.product_id')
        if (count($parts) < 2) {
            return;
        }

        $index = $parts[0]; // O índice do item (ex: 0)
        $field = $parts[1]; // O campo que mudou (ex: 'product_id' ou 'quantity')

        // Só recalcula se o 'product_id' ou 'quantity' mudou
        if (in_array($field, ['product_id', 'quantity'])) {
            
            $productId = $this->items[$index]['product_id'] ?? null;
            $quantity = (int) ($this->items[$index]['quantity'] ?? 1);
            $quantity = max(1, $quantity); // Garante que a quantidade seja pelo menos 1

            if ($productId) {
                // NÃO FAZ QUERY! Apenas busca na coleção já carregada no mount()
                $product = $this->allProducts->find($productId);

                if ($product) {
                    $unitPrice = (float) $product->price;
                    $this->items[$index]['unit_price'] = $unitPrice;
                    $this->items[$index]['subtotal'] = $unitPrice * $quantity;
                } else {
                    // Se o produto não for encontrado (improvável, mas seguro)
                    $this->items[$index]['unit_price'] = 0;
                    $this->items[$index]['subtotal'] = 0;
                }
            } else {
                // Se o produto for 'Selecione um produto' (ID nulo)
                $this->items[$index]['unit_price'] = 0;
                $this->items[$index]['subtotal'] = 0;
            }

            // Após calcular o subtotal do item, atualiza o total geral
            $this->updateTotal();
        }
    }

    private function updateTotal(): void
    {
        $this->total_amount = (float) collect($this->items)->sum(fn($i) => (float) ($i['subtotal'] ?? 0));
    }

    // ... (save e setSale são iguais) ...

    public function render()
    {
        // Agora, em vez de consultar o banco, apenas passamos
        // as propriedades que já carregamos no mount()
        return view('livewire.sales.form', [
            'customers' => $this->allCustomers,
            'products'  => $this->allProducts,
            'statuses'  => $this->allStatuses,
        ]);
    }
}