<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Models\Product;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'date',
        'total_amount',
        'status'
    ];

    public static function rules($id = null): array
    {
        return [
            'customer_id' => 'required|exists:customers,id',
            'date' => 'required|date',
            'total_amount' => 'required|numeric|min:0',
            'status' => ['required', Rule::in(array_keys(self::statuses()))],
        ];
    }

    public static function statuses(): array
    {
        return [
            'processando' => 'Processando',
            'em_producao' => 'Em produção',
            'pendente_pagamento' => 'Aguardando pagamento',
            'concluido' => 'Concluído',
            'cancelado' => 'Cancelado',
        ];
    }

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Restore stock for all items of this sale.
     */
    public function restoreStock(): void
    {
        // Use transaction to be safe if chamado isoladamente
        DB::transaction(function () {
            foreach ($this->items()->get() as $item) {
                Product::where('id', $item->product_id)
                    ->increment('stock', $item->quantity);
            }
        });
    }

    /**
     * Cancel the sale: restore stock and set status to 'cancelado'.
     */
    public function cancel(): void
    {
        DB::transaction(function () {
            // restore stock of items
            $this->restoreStock();

            // mark as canceled
            $this->update(['status' => 'cancelado']);
        });
    }

    /**
     * Safety: if a sale is deleted (shouldn't be in UI), restore stock first.
     */
    protected static function booted()
    {
        static::deleting(function (Sale $sale) {
            // restore stock to avoid data loss
            foreach ($sale->items()->get() as $item) {
                Product::where('id', $item->product_id)
                    ->increment('stock', $item->quantity);
            }
        });
    }
}
