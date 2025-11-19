<div class="container mx-auto p-6">

    <div class="mt-6 flex justify-center">
        <div class="w-full max-w-6xl bg-white shadow-xl rounded-2xl overflow-hidden p-4">

            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-800">Lista de Pedidos</h2>

                <button
                    wire:click="create"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-s font-semibold rounded-full hover:bg-blue-700 transition">
                    Novo Pedido +
                </button>
            </div>

            <div class="mb-4 flex gap-3">

                <input
                    type="text"
                    id="sale-search"
                    placeholder="Buscar por cliente, data, valor..."
                    class="block w-2/3 p-3 text-gray-900 border border-gray-300 rounded-lg bg-white text-base focus:ring-blue-500 focus:border-blue-500"
                    onkeyup="filterSales()"
                >

                <select
                    id="sale-status-filter"
                    class="block w-1/3 p-3 text-gray-900 border border-gray-300 rounded-lg bg-white text-base focus:ring-blue-500 focus:border-blue-500"
                    onchange="filterSales()"
                >
                    <option value="">Todos os estágios</option>
                    <option value="processando">Processando</option>
                    <option value="em_producao">Em produção</option>
                    <option value="pendente_pagamento">Aguardando pagamento</option>
                    <option value="pronto">Pronto p entrega</option>
                    <option value="concluido">Concluído</option>
                    <option value="cancelado">Cancelado</option>
                </select>

            </div>

            <table class="w-full max-w-6xl bg-white shadow-xl rounded-xl overflow-hidden">
                <thead class="bg-gray-300 text-xs uppercase text-black">
                    <tr>
                        <th class="px-5 py-3 text-center">ID</th>
                        <th class="px-5 py-3 text-center">Cliente</th>
                        <th class="px-5 py-3 text-center">Data</th>
                        <th class="px-5 py-3 text-center">Data Final</th>
                        <th class="px-5 py-3 text-center">Estágio</th>
                        <th class="px-5 py-3 text-center">Valor Total</th>
                        <th class="px-5 py-3 text-center">Ações</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200">
                    @foreach ($sales as $sale)
                        <tr
                            @class([
                                'sale-item hover:bg-gray-50 transition-opacity duration-200',
                                'opacity-50' => $sale->status === 'cancelado'
                            ])
                            data-id="{{ $sale->id }}"
                            data-customer="{{ strtolower($sale->customer->name ?? '') }}"
                            data-date="{{ \Carbon\Carbon::parse($sale->date)->format('d/m/Y') }}"
                            data-date-finish="{{ $sale->date_finish ? \Carbon\Carbon::parse($sale->date_finish)->format('d/m/Y') : '' }}"
                            data-status="{{ strtolower($sale->status) }}"
                            data-total="{{ number_format($sale->total_amount, 2, ',', '.') }}"
                        >
                            <td class="px-5 py-3 text-center">{{ $sale->id }}</td>
                            <td class="px-5 py-3 text-center">{{ $sale->customer->name ?? '—' }}</td>
                            <td class="px-5 py-3 text-center">{{ \Carbon\Carbon::parse($sale->date)->format('d/m/Y') }}</td>
                            <td class="px-5 py-3 text-center">
                                {{ $sale->date_finish ? \Carbon\Carbon::parse($sale->date_finish)->format('d/m/Y') : '—' }}
                            </td>

                            <td class="px-5 py-3 text-center">
                                {{ \App\Models\Sale::statuses()[$sale->status] ?? $sale->status }}
                            </td>

                            <td class="px-5 py-3 text-center">R$ {{ number_format($sale->total_amount, 2, ',', '.') }}</td>

                            <td class="px-5 py-3 text-center space-x-2">
                                <button wire:click="view({{ $sale->id }})"
                                    class="inline-flex items-center px-3 py-1.5 bg-green-500 text-white text-xs font-semibold rounded-full hover:bg-green-700 transition">
                                    Visualizar
                                </button>

                                <button wire:click="edit({{ $sale->id }})"
                                    @disabled($sale->status === 'cancelado')
                                    class="inline-flex items-center px-3 py-1.5 bg-gray-500 text-white text-xs font-semibold rounded-full hover:bg-gray-700 transition disabled:opacity-50 disabled:cursor-not-allowed">
                                    Editar
                                </button>

                                <button
                                    wire:click="cancel({{ $sale->id }})"
                                    @disabled($sale->status === 'cancelado')
                                    class="inline-flex items-center px-3 py-1.5 bg-red-600 text-white text-xs font-semibold rounded-full hover:bg-red-800 transition disabled:opacity-50 disabled:cursor-not-allowed">
                                    Cancelar
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

        </div>
    </div>

    <div class="mt-6 max-w-6xl mx-auto bg-white p-4 rounded-2xl shadow">
        {{ $sales->links() }}

        @if($sales->isEmpty())
            <div class="text-center text-gray-500 py-4">
                Nenhuma venda encontrada.
            </div>
        @endif
    </div>

    @if($showFormModal)
        <livewire:sales.form
            :sale="$selectedSale ?? new \App\Models\Sale()"
            wire:key="form-{{ $selectedSale->id ?? 'new' }}" />
    @endif

    @if ($showViewModal && $selectedSale)
        <livewire:sales.show
            :sale="$selectedSale"
            wire:key="view-{{ $selectedSale->id }}" />
    @endif

</div>

@once
<script src="//unpkg.com/alpinejs" defer></script>
@endonce

<script>
    function filterSales() {
        const search = document.getElementById('sale-search').value.toLowerCase().trim();
        const statusFilter = document.getElementById('sale-status-filter').value.toLowerCase().trim();

        const items = document.querySelectorAll('.sale-item');

        items.forEach(item => {
            const id = (item.dataset.id ?? '').toLowerCase();
            const customer = (item.dataset.customer ?? '').toLowerCase();
            const date = (item.dataset.date ?? '').toLowerCase();
            const dateFinish = (item.dataset.dateFinish ?? '') .toLowerCase(); // data-date-finish transforma em dataset.dateFinish
            const status = (item.dataset.status ?? '').toLowerCase();
            const total = (item.dataset.total ?? '').toLowerCase();

            const searchMatch =
                search === '' ||
                id.includes(search) ||
                customer.includes(search) ||
                date.includes(search) ||
                dateFinish.includes(search) ||
                status.includes(search) ||
                total.includes(search);

            const statusMatch =
                statusFilter === '' || status === statusFilter;

            item.style.display = (searchMatch && statusMatch) ? '' : 'none';
        });
    }
</script>
