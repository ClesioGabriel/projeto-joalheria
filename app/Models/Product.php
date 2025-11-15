<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Validation\Rule;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'description',
        'image',
        'stock',
        'type', // novo campo (material_prima | produto_final)
    ];

    /**
     * Regras de validação para produtos.
     *
     * @param int|null $id opcional para ignorar em unique (se houver)
     * @return array
     */
    public static function rules($id = null): array
    {
        return [
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'stock' => 'required|integer|min:0',
            // type deve estar nas chaves permitidas
            'type' => ['required', Rule::in(array_keys(self::types()))],
            // imagem é validada no componente (file) — aqui podemos apenas validar string/nullable
            'image' => 'nullable|string|max:255',
        ];
    }

    /**
     * Tipos de produto disponíveis (chave => label)
     */
    public static function types(): array
    {
        return [
            'material_prima' => 'Matéria-prima',
            'produto_final' => 'Produto final',
        ];
    }

    /**
     * Retorna somente as chaves dos tipos (útil para Rule::in)
     */
    public static function typesKeys(): array
    {
        return array_keys(self::types());
    }

    public function getPriceAttribute($value)
    {
        return (float) $value;
    }

    public function setPriceAttribute($value)
    {
        $this->attributes['price'] = (float) $value;
    }
}
