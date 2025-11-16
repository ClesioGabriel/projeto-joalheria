<div class="container mx-auto p-6">

    <div class="mt-6 flex justify-center">
        <div class="w-full max-w-6xl bg-white shadow-xl rounded-2xl overflow-hidden p-6">

            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-800">Lista de Produtos</h2>

                <button
                    wire:click="create"
                    wire:loading.attr="disabled"
                    wire:target="create"
                    class="inline-flex items-center px-4 py-2 bg-blue-500 text-white text-xs font-semibold rounded-full hover:bg-blue-700 transition disabled:opacity-50"
                >
                    Novo Produto
                </button>
            </div>

            <div class="mb-4">
                <input
                    type="text"
                    id="product-search"
                    placeholder="Buscar produtos por nome, preço, tipo, localização..."
                    class="block w-full p-3 text-gray-900 border border-gray-300 rounded-lg bg-white text-base focus:ring-blue-500 focus:border-blue-500"
                    onkeyup="filterProducts()"
                >
            </div>

            <table class="w-full bg-white shadow-xl rounded-xl overflow-hidden">
                <thead class="bg-gray-100 text-xs uppercase text-gray-600">
                    <tr>
                        <th class="px-5 py-3 text-center">ID</th>
                        <th class="px-5 py-3 text-center">Nome</th>
                        <th class="px-5 py-3 text-center">Imagem</th>
                        <th class="px-5 py-3 text-center">QTD. Estoque</th>
                        <th class="px-5 py-3 text-center">Localização</th>
                        <th class="px-5 py-3 text-center">Preço</th>
                        <th class="px-5 py-3 text-center">Ações</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200">

                    @foreach ($products as $product)

                        <tr class="hover:bg-gray-50 transition product-item"
                            data-name="{{ strtolower($product->name) }}"
                            data-price="{{ strtolower(number_format($product->price, 2, ',', '.')) }}"
                            data-type="{{ strtolower($product->type ?? '') }}"
                            data-location="{{ strtolower($product->location ?? '') }}"
                        >
                            <td class="px-5 py-3 text-center">{{ $product->id }}</td>

                            <td class="px-5 py-3 text-center">{{ $product->name }}</td>

                            <td class="px-5 py-3 text-center">
                                @if ($product->photo_path)
                                    <img src="{{ asset('storage/' . $product->photo_path) }}"
                                         alt="{{ $product->name }}"
                                         class="w-16 h-16 object-cover rounded-md mx-auto shadow-sm">
                                @else
                                    <span class="text-gray-400 text-xs">Sem imagem</span>
                                @endif
                            </td>

                            <td class="px-5 py-3 text-center">{{ $product->stock }}</td>

                            <td class="px-5 py-3 text-center">{{ $product->location }}</td>

                            <td class="px-5 py-3 text-center">
                                R$ {{ number_format($product->price, 2, ',', '.') }}
                            </td>

                            <td class="px-5 py-3 text-center space-x-2">
                                <button
                                    wire:click="view({{ $product->id }})"
                                    wire:loading.attr="disabled"
                                    wire:target="view({{ $product->id }})"
                                    class="inline-flex items-center px-3 py-1.5 bg-green-500 text-white text-xs font-semibold rounded-full hover:bg-green-700 transition disabled:opacity-50">
                                    Visualizar
                                </button>

                                <button
                                    wire:click="edit({{ $product->id }})"
                                    wire:loading.attr="disabled"
                                    wire:target="edit({{ $product->id }})"
                                    class="inline-flex items-center px-3 py-1.5 bg-gray-500 text-white text-xs font-semibold rounded-full hover:bg-gray-700 transition disabled:opacity-50">
                                    Editar
                                </button>

                                <button
                                    wire:click="delete({{ $product->id }})"
                                    onclick="return confirm('Tem certeza que deseja deletar?')"
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

<script>
    function filterProducts() {
        const search = document.getElementById('product-search').value.toLowerCase();
        const items = document.querySelectorAll('.product-item');

        items.forEach(item => {
            const name = item.dataset.name || '';
            const price = item.dataset.price || '';
            const type = item.dataset.type || '';
            const location = item.dataset.location || '';

            const match =
                name.includes(search) ||
                price.includes(search) ||
                type.includes(search) ||
                location.includes(search);

            item.style.display = match ? '' : 'none';
        });
    }
</script>
