<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

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
}
