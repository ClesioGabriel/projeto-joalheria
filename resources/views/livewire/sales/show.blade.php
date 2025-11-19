<div 
    x-data="{ open: true }"
    x-show="open"
    x-transition.opacity
    wire:click.self="$dispatch('close-view-modal')"
    x-on:keydown.escape.window="open = false; $dispatch('close-view-modal')"
    class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center"
>
    <div class="p-8 max-w-2xl mx-auto bg-white rounded-2xl shadow-lg border border-gray-200 text-left relative z-50">
        <h2 class="text-3xl font-bold text-gray-800 mb-6">Detalhes do Pedido</h2>

        <div class="space-y-4 text-gray-700 text-lg">
            <div><span class="font-semibold">👤 Cliente:</span> {{ $sale->customer->name ?? '—' }}</div>
            <div><span class="font-semibold">📅 Data:</span> {{ \Carbon\Carbon::parse($sale->date)->format('d/m/Y') }}</div>
            <div><span class="font-semibold">🏁 Data Final:</span> {{ $sale->date_finish ? \Carbon\Carbon::parse($sale->date_finish)->format('d/m/Y') : '—' }}</div>
            <div><span class="font-semibold">💰 Valor Total:</span> R$ {{ number_format($sale->total_amount, 2, ',', '.') }}</div>
            <div><span class="font-semibold">📍 Estágio:</span> {{ \App\Models\Sale::statuses()[$sale->status] ?? $sale->status }}</div>
        </div>

        <h3 class="mt-6 text-xl font-semibold text-gray-800">Itens</h3>
        <table class="w-full mt-3 border-collapse">
            <thead>
                <tr class="bg-gray-100 text-gray-700">
                    <th class="py-2 px-4 text-left">Produto</th>
                    <th class="py-2 px-4 text-center">Qtd.</th>
                    <th class="py-2 px-4 text-center">Preço</th>
                    <th class="py-2 px-4 text-center">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($sale->items as $item)
                    <tr class="border-t">
                        <td class="py-2 px-4">{{ $item->product->name }}</td>
                        <td class="py-2 px-4 text-center">{{ $item->quantity }}</td>
                        <td class="py-2 px-4 text-center">R$ {{ number_format($item->unit_price, 2, ',', '.') }}</td>
                        <td class="py-2 px-4 text-center">R$ {{ number_format($item->subtotal, 2, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-8 flex justify-end">
            <button
                wire:click="$dispatch('close-view-modal')"
                class="px-4 py-2 bg-blue-500 text-white rounded-lg shadow hover:bg-blue-700 transition"
            >
                Fechar
            </button>
        </div>
    </div>
</div>
