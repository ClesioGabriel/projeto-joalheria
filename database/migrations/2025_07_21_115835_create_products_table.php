<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('product_type')->default('finished_product'); // finished_product | raw_material

            $table->decimal('price', 10, 2)->default(0.00);
            $table->integer('stock')->default(0);

            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->string('photo_path')->nullable();

            $table->string('metal')->nullable();
            $table->decimal('weight', 10, 2)->nullable();
            $table->string('stone_type')->nullable();
            $table->string('stone_size')->nullable();

            $table->string('serial_number')->nullable()->unique();
            $table->string('location')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
