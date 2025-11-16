<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Validation\Rule;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'cpf',
    ];

     public static function rules($id = null): array
    {
        return [
            'cpf' => [
                'required',
                'string',
                'size:11',
                Rule::unique('customers', 'cpf')->ignore($id),
            ],
            'name' => 'required|string|max:120',
            'email' => [
                'required',
                'email',
                Rule::unique('customers', 'email')->ignore($id),
            ],
            'phone' => 'nullable|string|max:20',
        ];
    }

    public static function messages(): array
    {
        return [
            'cpf.required' => 'O CPF é obrigatório.',
            'cpf.size' => 'O CPF deve ter exatamente 11 dígitos.',
            'cpf.unique' => 'Este CPF já está em uso.',
            'name.required' => 'O nome é obrigatório.',
            'email.required' => 'O e-mail é obrigatório.',
            'email.email' => 'Digite um e-mail válido.',
            'email.unique' => 'Esse e-mail já está em uso.',
        ];
    }

    /**
     * Retorna as regras para o formulário que inclui endereços
     * Exemplo de uso no Livewire:
     * $rules = array_merge(Customer::rules($id), $addressRulesMapped);
     */
    public static function rulesWithAddresses($id = null): array
    {
        $rules = self::rules($id);

        // transformar Address::rules() => addresses.*.field
        $addressRules = [];
        foreach (Address::rules() as $field => $rule) {
            $addressRules["addresses.*.{$field}"] = $rule;
        }

        return array_merge($rules, $addressRules);
    }

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function addresses()
{
    return $this->belongsToMany(Address::class, 'address_customer');
}
}
