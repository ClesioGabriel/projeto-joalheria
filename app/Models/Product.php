<?php

namespace App\Models;

    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Builder;

    class Product extends Model
    {
        use HasFactory;

        // product_type constants
        public const TYPE_FINISHED = 'finished_product';
        public const TYPE_RAW = 'raw_material';

        /**
         * The attributes that are mass assignable.
         *
         * @var array<int,string>
         */
        protected $fillable = [
            'name',
            'product_type',
            'price',
            'stock',
            'description',
            'image',
            'photo_path',
            'metal',
            'weight',
            'stone_type',
            'stone_size',
            'serial_number',
            'location',
        ];

        /**
         * The attributes that should be cast to native types.
         *
         * @var array<string,string>
         */
        protected $casts = [
            'price' => 'float',
            'weight' => 'float',
            'stock' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];

        /**
         * Default attributes.
         *
         * @var array<string,mixed>
         */
        protected $attributes = [
            'product_type' => self::TYPE_FINISHED,
            'price' => 0.00,
            'stock' => 0,
        ];

        // -------------------------
        // Scopes
        // -------------------------

        /**
         * Scope a query to only finished products.
         */
        public function scopeFinished(Builder $query): Builder
        {
            return $query->where('product_type', self::TYPE_FINISHED);
        }

        /**
         * Scope a query to only raw materials.
         */
        public function scopeRawMaterials(Builder $query): Builder
        {
            return $query->where('product_type', self::TYPE_RAW);
        }

        // -------------------------
        // Helpers
        // -------------------------

        /**
         * Return true if this product is a finished product.
         */
        public function isFinished(): bool
        {
            return $this->product_type === self::TYPE_FINISHED;
        }

        /**
         * Return true if this product is a raw material.
         */
        public function isRawMaterial(): bool
        {
            return $this->product_type === self::TYPE_RAW;
        }

        // -------------------------
        // Accessors / Mutators
        // -------------------------

        /**
         * Ensure price is always returned as float.
         */
        public function getPriceAttribute($value): float
        {
            return (float) $value;
        }

        /**
         * Normalize price on set.
         */
        public function setPriceAttribute($value): void
        {
            $this->attributes['price'] = is_numeric($value) ? (float) $value : 0.0;
        }

        /**
         * Normalize weight on set.
         */
        public function setWeightAttribute($value): void
        {
            $this->attributes['weight'] = $value === null ? null : (float) $value;
        }

        /**
         * Normalize serial number (uppercase, trim).
         */
        public function setSerialNumberAttribute($value): void
        {
            $this->attributes['serial_number'] = $value === null ? null : strtoupper(trim($value));
        }

        /**
         * Optional: formatted price for display (e.g. 1,234.56).
         */
        public function getFormattedPriceAttribute(): string
        {
            return number_format($this->price, 2, '.', ',');
        }
    }
