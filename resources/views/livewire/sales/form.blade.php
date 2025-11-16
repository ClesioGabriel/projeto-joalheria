<div 
    x-data="{ open: true }"
    x-show="open"
    x-transition.opacity
    wire:click.self="$dispatch('close-form')"
    x-on:keydown.escape.window="open = false; $dispatch('close-form')"
    class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-50"
>
    <div class="bg-white w-full max-w-2xl rounded-2xl shadow-xl p-8 relative">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">
            {{ $sale && $sale->exists ? 'Editar Venda' : 'Nova Venda' }}
        </h2>

        <form wire:submit.prevent="save" class="space-y-6">
            
            {{-- Cliente --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">Cliente</label>
                <select wire:model.defer="customer_id" class="w-full border rounded-lg px-3 py-2">
                    <option value="">Selecione um cliente</option>
                    @foreach ($customers as $customer)
                        <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                    @endforeach
                </select>
                @error('customer_id') 
                    <span class="text-red-500 text-sm">{{ $message }}</span> 
                @enderror
            </div>

            {{-- Data --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">Data</label>
                <input type="date" wire:model.defer="date" class="w-full border rounded-lg px-3 py-2">
                @error('date') 
                    <span class="text-red-500 text-sm">{{ $message }}</span> 
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Estágio</label>
                <select wire:model.defer="stage" class="w-full border rounded-lg px-3 py-2">
                    <option value="">Selecione um estágio</option>
                    <option value="Aguardando Pagamento">Aguardando Pagamento</option>
                    <option value="Cancelado">Cancelado</option>
                    <option value="Concluído">Concluído</option>
                </select>
                @error('stage') 
                    <span class="text-red-500 text-sm">{{ $message }}</span> 
                @enderror
            </div>

            {{-- Itens --}}
            <div>
                <h3 class="font-semibold text-lg text-gray-800 mb-3">Itens do Pedido</h3>
                @foreach ($items as $index => $item)
                    <div class="grid grid-cols-4 gap-4 mb-3 items-center">
                        <select wire:model="items.{{ $index }}.product_id" class="col-span-2 border rounded-lg px-3 py-2">
                            <option value="">Selecione um produto</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                            @endforeach
                        </select>
                        <input type="number" min="1" wire:model="items.{{ $index }}.quantity" class="border rounded-lg px-3 py-2" />
                        <span class="text-gray-700">R$ {{ number_format($items[$index]['subtotal'] ?? 0, 2, ',', '.') }}</span>
                        <button type="button" wire:click="removeItem({{ $index }})" class="text-red-600 hover:text-red-800">✕</button>
                    </div>
                @endforeach
                <button type="button" wire:click="addItem" class="mt-2 px-3 py-1 bg-green-500 text-white rounded-lg hover:bg-green-600">
                    + Adicionar Item
                </button>
            </div>

            {{-- Total --}}
            <div class="flex justify-between items-center border-t pt-4 mt-4">
                <span class="font-bold text-lg text-gray-800">Total:</span>
                <span class="text-xl font-semibold text-blue-600">
                    R$ {{ number_format($total_amount, 2, ',', '.') }}
                </span>
            </div>

            {{-- Botões --}}
            <div class="flex justify-end space-x-3 mt-6">
                <button 
                    type="button"
                    x-on:click="open = false; $dispatch('close-form')"
                    class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg shadow hover:bg-gray-400 transition"
                >
                    Cancelar
                </button>
                <button 
                    type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 transition"
                >
                    Salvar Pedido
                </button>
            </div>
        </form>
    </div>
</div>
