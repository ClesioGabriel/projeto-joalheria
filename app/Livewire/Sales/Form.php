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
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class Form extends Component
{
    public ?int $customer_id = null;
    public string $date;
    public array $items = [];
    public float $total_amount = 0;
    public ?Sale $sale = null;
    public ?string $status = null;

    /** @var Collection<int,Product> */
    public Collection $allProducts;

    /** @var Collection<int,Customer> */
    public Collection $allCustomers;

    public array $allStatuses = [];

    public function mount(?Sale $sale = null)
    {
        // load collections once
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
                'quantity'   => (int) $i->quantity,
                'unit_price' => (float) $i->unit_price,
                'subtotal'   => (float) $i->subtotal,
            ])->toArray();
            $this->updateTotal();
        } else {
            $this->sale = null;
            $this->date = now()->format('Y-m-d');
            $this->items = [];
            $this->status = null;
            $this->total_amount = 0;
        }
    }

    /**
     * Accepts remote events to set the sale to edit.
     * Payload can be: null, int id, array ['id'=>..], or object/array with id key.
     */
    #[On('set-sale')]
    public function setSale($payload): void
    {
        $id = null;

        if (is_null($payload)) {
            $id = null;
        } elseif (is_array($payload)) {
            $id = $payload['id'] ?? null;
        } elseif (is_numeric($payload)) {
            $id = (int) $payload;
        } elseif (is_object($payload) && isset($payload->id)) {
            $id = $payload->id;
        } elseif (is_object($payload) && isset($payload['id'])) {
            $id = $payload['id'];
        }

        if ($id) {
            $sale = Sale::with('items')->find($id);
            if ($sale) {
                // re-use mount logic to populate
                $this->mount($sale);
                return;
            }
        }

        // new sale
        $this->mount(null);
    }

    protected function rules(): array
    {
        return [
            'customer_id'            => 'required|exists:customers,id',
            'date'                   => 'required|date',
            'status'                 => 'nullable|string',
            'items'                  => 'required|array|min:1',
            'items.*.product_id'     => 'required|exists:products,id',
            'items.*.quantity'       => 'required|integer|min:1',
            'items.*.unit_price'     => 'required|numeric|min:0',
            'items.*.subtotal'       => 'required|numeric|min:0',
        ];
    }

    /**
     * Add a blank item to the sale.
     */
    public function addItem(): void
    {
        $this->items[] = [
            'product_id' => null,
            'quantity' => 1,
            'unit_price' => 0.0,
            'subtotal' => 0.0,
        ];
    }

    /**
     * Remove item at index and reindex array.
     */
    public function removeItem(int $index): void
    {
        if (! isset($this->items[$index])) {
            return;
        }

        array_splice($this->items, $index, 1);
        $this->updateTotal();
    }

    /**
     * Hook called by Livewire when an item array field changes.
     * $key example: "0.product_id" or "1.quantity"
     */
    public function updatedItems($value, $key): void
    {
        $parts = explode('.', $key);

        if (count($parts) < 2) {
            return;
        }

        $index = (int) $parts[0];
        $field = $parts[1];

        if (! isset($this->items[$index])) {
            return;
        }

        // Ensure quantity is at least 1 when changed
        if ($field === 'quantity') {
            $quantity = (int) ($this->items[$index]['quantity'] ?? 1);
            $quantity = max(1, $quantity);
            $this->items[$index]['quantity'] = $quantity;
        }

        if (in_array($field, ['product_id', 'quantity'])) {
            $productId = $this->items[$index]['product_id'] ?? null;
            $quantity = (int) ($this->items[$index]['quantity'] ?? 1);
            $quantity = max(1, $quantity);

            if ($productId) {
                // use preloaded collection to avoid DB hit
                $product = $this->allProducts->firstWhere('id', $productId);

                if ($product) {
                    // If product out of stock: disallow selection and notify
                    if ((int)$product->stock <= 0) {
                        // reset selection
                        $this->items[$index]['product_id'] = null;
                        $this->items[$index]['unit_price'] = 0.0;
                        $this->items[$index]['quantity'] = 1;
                        $this->items[$index]['subtotal'] = 0.0;
                        $this->updateTotal();
                        $this->dispatch('notify', ['message' => "Produto \"{$product->name}\" sem estoque.", 'type' => 'error']);
                        return;
                    }

                    $unitPrice = (float) $product->price;

                    // If requested quantity exceeds stock, cap it
                    if ($quantity > (int)$product->stock) {
                        $quantity = (int)$product->stock;
                        $this->items[$index]['quantity'] = $quantity;
                        $this->dispatch('notify', ['message' => "Quantidade ajustada: apenas {$product->stock} disponível(s) para {$product->name}.", 'type' => 'warning']);
                    }

                    $this->items[$index]['unit_price'] = $unitPrice;
                    $this->items[$index]['subtotal'] = round($unitPrice * $quantity, 2);
                } else {
                    // product not found in memory
                    $this->items[$index]['unit_price'] = 0.0;
                    $this->items[$index]['subtotal'] = 0.0;
                }
            } else {
                // no product selected
                $this->items[$index]['unit_price'] = 0.0;
                $this->items[$index]['subtotal'] = 0.0;
            }

            $this->updateTotal();
        }
    }

    /**
     * Recalculate total_amount based on items[] subtotals.
     */
    private function updateTotal(): void
    {
        $this->total_amount = (float) collect($this->items)->sum(fn($i) => (float) ($i['subtotal'] ?? 0));
    }

    /**
     * Persist sale and its items with stock control.
     */
    public function save(): void
    {
        // Recalculate total to be safe (ensures we send total_amount to DB)
        $this->updateTotal();

        $this->validate();

        // Validate existence of customer
        if (! $this->customer_id || ! Customer::find($this->customer_id)) {
            throw ValidationException::withMessages(['customer_id' => 'Cliente inválido.']);
        }

        DB::transaction(function () {
            // include total_amount here to avoid DB NOT NULL error
            $dataSale = [
                'customer_id' => $this->customer_id,
                'date' => $this->date,
                'status' => $this->status,
                'total_amount' => $this->total_amount,
            ];

            // Prepare sale variable
            $sale = null;

            // If editing existing sale: restore previous stock first
            if ($this->sale && $this->sale->exists) {
                foreach ($this->sale->items as $oldItem) {
                    Product::where('id', $oldItem->product_id)
                        ->increment('stock', $oldItem->quantity);
                }

                $this->sale->update($dataSale);
                $sale = $this->sale;

                // delete old items (we restored stock already)
                $sale->items()->delete();
            } else {
                $sale = Sale::create($dataSale);
                $this->sale = $sale;
            }

            // Aggregate requested qty per product (handles same product on multiple lines)
            $requested = [];
            foreach ($this->items as $it) {
                $pid = $it['product_id'];
                $qty = (int) ($it['quantity'] ?? 0);
                if (! $pid) {
                    throw ValidationException::withMessages(['items' => 'Produto inválido em um dos itens.']);
                }
                $requested[$pid] = ($requested[$pid] ?? 0) + $qty;
            }

            if (empty($requested)) {
                throw ValidationException::withMessages(['items' => 'Adicione pelo menos um item com produto válido.']);
            }

            // Lock rows for update and check stock availability
            $productIds = array_keys($requested);
            $lockedProducts = Product::whereIn('id', $productIds)->lockForUpdate()->get()->keyBy('id');

            foreach ($requested as $pid => $qty) {
                $prod = $lockedProducts->get($pid);
                if (! $prod) {
                    throw ValidationException::withMessages(['items' => "Produto inválido (id: {$pid})."]);
                }
                if ($qty > (int) $prod->stock) {
                    throw ValidationException::withMessages(['items' => "Estoque insuficiente para o produto \"{$prod->name}\". Disponível: {$prod->stock}, solicitado: {$qty}"]);
                }
            }

            // Persist items and decrement stock
            foreach ($this->items as $it) {
                $quantity  = (int) ($it['quantity'] ?? 0);
                $unitPrice = (float) ($it['unit_price'] ?? 0.0);
                $subtotal  = (float) ($it['subtotal'] ?? ($unitPrice * $quantity));

                $sale->items()->create([
                    'product_id' => $it['product_id'],
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'subtotal' => $subtotal,
                ]);

                // decrement stock (use locked model to avoid race)
                $lockedProducts->get($it['product_id'])->decrement('stock', $quantity);
            }

            // update sale totals if model has these fillable fields
            if (in_array('total_amount', $sale->getFillable())) {
                $sale->update(['total_amount' => $this->total_amount]);
            } elseif (in_array('total', $sale->getFillable())) {
                $sale->update(['total' => $this->total_amount]);
            }
        });

        // events / notifications
        $this->dispatch('sale-saved');
        $this->dispatch('close-form-modal');
    }

    public function render()
    {
        return view('livewire.sales.form', [
            'customers' => $this->allCustomers,
            'products' => $this->allProducts,
            'statuses' => $this->allStatuses,
        ]);
    }
}
