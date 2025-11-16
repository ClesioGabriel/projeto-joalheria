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

class Form extends Component
{
    public ?int $customer_id = null;
    public string $date;
    public array $items = [];
    public float $total_amount = 0;
    public ?Sale $sale = null;
    public ?string $status = null;

    public function mount(?Sale $sale = null)
    {
        if ($sale && $sale->exists) {
            $this->sale = $sale->load('items');
            $this->customer_id = $sale->customer_id;
            $this->date = \Carbon\Carbon::parse($sale->date)->format('Y-m-d');
            $this->status = $sale->status;
            $this->items = $sale->items->map(fn($i) => [
                'product_id' => $i->product_id,
                'quantity'   => $i->quantity,
                'unit_price' => (float) $i->unit_price,
                'subtotal'   => (float) $i->subtotal,
            ])->toArray();
            $this->updateTotal();
        } else {
            $this->sale = null;
            $this->date = now()->format('Y-m-d');
            $this->items = [];
            $this->status = null;
        }
    }

    protected function validationRules(): array
    {
        // Base rules from model (do not duplicate)
        $saleRules = Sale::rules($this->sale->id ?? null);

        // make status optional in the form (we won't force it here)
        $saleRules['status'] = 'nullable';

        // Map SaleItem rules into items.*.field (skip sale_id)
        $itemRules = [];
        foreach (SaleItem::rules() as $field => $rule) {
            if ($field === 'sale_id') {
                continue;
            }
            $itemRules["items.*.{$field}"] = $rule;
        }

        return array_merge($saleRules, $itemRules);
    }

    public function addItem(): void
    {
        $this->items[] = [
            'product_id' => null,
            'quantity' => 1,
            'unit_price' => 0,
            'subtotal' => 0,
        ];
    }

    public function removeItem(int $index): void
    {
        if (! isset($this->items[$index])) {
            return;
        }
        array_splice($this->items, $index, 1);
        $this->updateTotal();
    }

    /**
     * Livewire calls updated* hooks when a property changes.
     * We'll recompute unit_price/subtotal whenever items change.
     */
    public function updatedItems(): void
    {
        foreach ($this->items as $idx => $item) {
            if (! empty($item['product_id'])) {
                $product = Product::find($item['product_id']);
                if ($product) {
                    $unit = $product->price;
                    $qty = max(1, (int) ($item['quantity'] ?? 1));
                    $this->items[$idx]['unit_price'] = (float) $unit;
                    $this->items[$idx]['subtotal'] = (float) ($unit * $qty);
                }
            } else {
                $this->items[$idx]['unit_price'] = 0;
                $this->items[$idx]['subtotal'] = 0;
            }
        }

        $this->updateTotal();
    }

    private function updateTotal(): void
    {
        $this->total_amount = (float) collect($this->items)->sum(fn($i) => (float) ($i['subtotal'] ?? 0));
    }

    public function save(): void
    {
        $rules = $this->validationRules();

        $this->validate($rules);

        DB::transaction(function () {
            // create or update sale (default status if not provided)
            $payload = [
                'customer_id' => $this->customer_id,
                'date' => $this->date,
                'total_amount' => $this->total_amount,
                'status' => $this->status ?? 'pendente_pagamento',
            ];

            if ($this->sale && $this->sale->exists) {
                $this->sale->update($payload);
                $this->sale->items()->delete();
            } else {
                $this->sale = Sale::create($payload);
            }

            // persistir itens (garantir unit_price/subtotal coerentes)
            foreach ($this->items as $item) {
                $product = Product::find($item['product_id']);
                $unitPrice = $product ? (float) $product->price : (float) ($item['unit_price'] ?? 0);
                $quantity = (int) ($item['quantity'] ?? 1);
                $subtotal = (float) ($item['subtotal'] ?? ($unitPrice * $quantity));

                $this->sale->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'subtotal' => $subtotal,
                ]);
            }
        });

        $this->dispatch('notify', ['message' => 'Venda salva com sucesso!']);
        $this->dispatch('sale-saved');

        // opcional: resetar formulário
        $this->mount(null);
    }

    #[On('set-sale')]
    public function setSale(Sale $sale): void
    {
        $this->mount($sale);
    }

    public function render()
    {
        return view('livewire.sales.form', [
            'customers' => Customer::orderBy('name')->get(),
            'products'  => Product::orderBy('name')->get(),
            'statuses'  => Sale::statuses(),
        ]);
    }
}
