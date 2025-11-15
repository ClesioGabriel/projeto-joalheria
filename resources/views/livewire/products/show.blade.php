{{-- 
  Este componente só é renderizado se o @if no index.blade.php for verdadeiro.
  Portanto, ele é o próprio modal.
--}}
<div 
    class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-50"
    style="overflow-y: auto;"
    x-data
    {{-- Isso permite fechar o modal com a tecla 'Escape' --}}
    x-on:keydown.escape.window="$dispatch('close-view-modal')"
>
    <div class="bg-white w-full max-w-lg rounded-lg shadow p-6 my-8">
        
        {{-- Verificamos se o produto está carregado (o que já deve estar) --}}
        @if ($product)
            
            <!-- 1. FOTO DO PRODUTO -->
            @if ($product->photo_path)
                <div class="mb-4 w-full flex justify-center">
                    <img src="{{ asset('storage/' . $product->photo_path) }}" 
                         alt="{{ $product->name }}" 
                         class="max-w-xs w-full h-auto rounded-lg shadow-md object-cover">
                </div>
            @else
                <div class="mb-4 p-4 text-center bg-gray-100 rounded-lg">
                    <span class="text-gray-500">Sem foto cadastrada</span>
                </div>
            @endif

            <!-- 2. INFORMAÇÕES BÁSICAS -->
            <h2 class="text-2xl font-bold mb-3">
                🛍️ {{ $product->name }}
            </h2>
            
            <p class="text-xl text-green-700 font-semibold mb-3">
                💰 R$ {{ number_format($product->price, 2, ',', '.') }}
            </p>
            
            @if($product->description)
                <p class="text-gray-700 mb-4">
                    📝 <strong>Descrição:</strong> {{ $product->description }}
                </p>
            @endif

            <hr class="my-4">

            <!-- 3. NOVOS CAMPOS (DETALHES DA PEÇA) -->
            <h3 class="text-lg font-semibold mb-2">Detalhes da Peça</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-2 text-gray-800">

                @if ($product->serial_number)
                    <p><strong>Nº de Série:</strong> {{ $product->serial_number }}</p>
                @endif

                @if ($product->location)
                    <p><strong>Localização:</strong> {{ $product->location }}</p>
                @endif

                @if ($product->metal)
                    <p><strong>Metal:</strong> {{ $product->metal }}</p>
                @endif

                @if ($product->weight)
                    <p><strong>Peso:</strong> {{ $product->weight }} g</p>
                @endif

                @if ($product->stone_type)
                    <p><strong>Tipo da Pedra:</strong> {{ $product->stone_type }}</p>
                @endif

                @if ($product->stone_size)
                    <p><strong>Tamanho da Pedra:</strong> {{ $product->stone_size }}</p>
                @endif
            </div>
            
            <!-- BOTÃO DE FECHAR -->
            <div class="flex justify-end mt-6">
                <button 
                    type="button" 
                    {{-- Chama o método 'closeModal' no Show.php --}}
                    wire:click="closeModal" 
                    class="inline-flex items-center px-4 py-2 bg-gray-500 text-white text-xs font-semibold rounded-full hover:bg-gray-700 transition"
                >
                    Fechar
                </button>
            </div>

        @else
            <div class="p-4 text-center text-red-500">
                Erro: Não foi possível carregar os dados do produto.
            </div>
        @endif
    </div>
</div>