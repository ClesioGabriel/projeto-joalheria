<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = [
        'customer_id',
        'street',
        'number',
        'neighborhood',
        'city',
        'state',
        'cep'
    ];

    public static function rules($id = null)
    {

        return [
            'street' => 'string|max:250',
            'number' => 'string|max:50',
            'neighborhood' => 'string|max:100',
            'city' => 'string|max:50',
            'state' => 'string|max:50',
            'cep'  => 'string|max:8'
        ];
    }

    

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
