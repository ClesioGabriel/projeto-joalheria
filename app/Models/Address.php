<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Address extends Model
{
    use HasFactory;

    // remove customer_id daqui: pivot table fará a associação
    protected $fillable = [
        'street',
        'number',
        'neighborhood',
        'city',
        'state',
        'cep',
    ];

    public static function rules($id = null)
    {
        return [
            'street' => 'nullable|string|max:150',
            'number' => 'nullable|string|max:50',
            'neighborhood' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:50',
            'state' => 'nullable|string|max:50',
            'cep'  => 'nullable|string|max:20',
        ];
    }

    /**
     * Many-to-many: an address can belong to many customers.
     */
    public function customers()
    {
        return $this->belongsToMany(Customer::class, 'address_customer');
    }
}
