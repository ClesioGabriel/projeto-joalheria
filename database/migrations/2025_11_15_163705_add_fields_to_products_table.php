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
        Schema::table('products', function (Blueprint $table) {
            // Adiciona novos campos após a coluna 'description'
            $table->string('metal')->nullable()->after('description'); // Ex: Ouro 18k, Prata 925
            $table->decimal('weight', 10, 2)->nullable()->after('metal'); // Peso em gramas
            $table->string('stone_type')->nullable()->after('weight'); // Ex: Diamante, Rubi
            $table->string('stone_size')->nullable()->after('stone_type'); // Ex: 0.5ct, 5mm
            $table->string('photo_path')->nullable()->after('stone_size'); // Caminho para a foto
            $table->string('serial_number')->unique()->nullable()->after('photo_path'); // Código individual
            $table->string('location')->nullable()->after('serial_number'); // Ex: Vitrine, Cofre
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'metal',
                'weight',
                'stone_type',
                'stone_size',
                'photo_path',
                'serial_number',
                'location'
            ]);
        });
    }
};