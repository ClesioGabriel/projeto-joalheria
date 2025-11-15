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
    ];

    public static function rules($id = null): array
    {
        return [
            'name' => 'required|string|max:255',
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
        return $this->hasMany(Address::class);
    }
}
