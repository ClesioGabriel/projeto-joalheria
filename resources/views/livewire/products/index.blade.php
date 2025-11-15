<div class="container mx-auto p-6">

<div class="mt-6 flex justify-center">
    <div class="w-full max-w-6xl bg-white shadow-xl rounded-2xl overflow-hidden p-4">
        
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold text-gray-800">Lista de Produtos</h2>
            <button
                wire:click="create"
                class="inline-flex items-center px-4 py-2 bg-blue-500 text-white text-xs font-semibold rounded-full hover:bg-blue-700 transition"
            >
                Novo Produto
            </button>
        </div>

        <table class="w-full max-w-6xl bg-white shadow-xl rounded-xl overflow-hidden">
            <thead class="bg-gray-100 text-xs uppercase text-gray-600">
                <tr>
                    <th class="px-5 py-3 text-center">ID</th>
                    <th class="px-5 py-3 text-center">Nome</th>
                    <th class="px-5 py-3 text-center">Preço</th>
                    <th class="px-5 py-3 text-center">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach ($products as $product)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-5 py-3 text-center">{{ $product->id }}</td>
                        <td class="px-5 py-3 text-center">{{ $product->name }}</td>
                        <td class="px-5 py-3 text-center">R$ {{ number_format($product->price, 2, ',', '.') }}</td>
                        <td class="px-5 py-3 text-center space-x-2">
                            {{-- Este botão está correto, ele chama o método 'view' no Index.php --}}
                            <button wire:click="view({{ $product->id }})"
                                class="inline-flex items-center px-3 py-1.5 bg-green-500 text-white text-xs font-semibold rounded-full hover:bg-green-700 transition">
                                Visualizar
                            </button>
                            <button wire:click="edit({{ $product->id }})"
                                class="inline-flex items-center px-3 py-1.5 bg-gray-500 text-white text-xs font-semibold rounded-full hover:bg-gray-700 transition">
                                Editar
                            </button>
                            <button wire:click="delete({{ $product->id }})" onclick="return confirm('Tem certeza que deseja deletar?')"
                                class="inline-flex items-center px-3 py-1.5 bg-red-600 text-white text-xs font-semibold rounded-full hover:bg-red-800 transition">
                                Excluir
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>


    <div class="mt-6 max-w-6xl mx-auto bg-white p-4 rounded-2xl shadow">
        {{ $products->links() }}

        @if($products->isEmpty())
            <div class="text-center text-gray-500 py-4">
                Nenhum produto encontrado.
            </div>
        @endif
    </div>

    @if($showFormModal)
    <livewire:products.form 
        :product="$selectedProduct ?? new \App\Models\Product()" 
        wire:key="form-{{ $selectedProduct->id ?? 'new' }}" 
    />
    @endif

    {{-- **** CORREÇÃO AQUI **** --}}
    {{-- 
      Se o modal de visualização deve ser exibido E o produto foi selecionado...
      Renderize o componente 'show', passando APENAS o produto.
    --}}
    @if ($showViewModal && $selectedProduct)
        <livewire:products.show
            :product="$selectedProduct" 
            wire:key="view-{{ $selectedProduct->id }}"
        />
    @endif

</div>

@once
    <script src="//unpkg.com/alpinejs" defer></script>
@endonce