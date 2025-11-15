<div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center" wire:click.self="$set('showViewModal', false)">
    <div class="p-8 max-w-2xl mx-auto bg-white rounded-2xl shadow-lg border border-gray-200 text-center relative z-50" wire:click.stop>
        <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center gap-2">
            <svg class="h-7 w-7 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 17v-6h6v6m-6 4h6a2 2 0 002-2V7a2 2 0 00-2-2h-3l-2-2H9a2 2 0 00-2 2v14a2 2 0 002 2z"/>
            </svg>
            Detalhes do Endereço
        </h2>

        <div class="space-y-3 text-gray-700 text-left">
            <div><span class="font-semibold">🏠 Rua:</span> {{ $address->street }}, {{ $address->number }}</div>
            <div><span class="font-semibold">📍 Bairro:</span> {{ $address->district }}</div>
            <div><span class="font-semibold">🏙️ Cidade:</span> {{ $address->city }}</div>
            <div><span class="font-semibold">🌎 Estado:</span> {{ $address->state }}</div>
            <div><span class="font-semibold">📮 CEP:</span> {{ $address->cep }}</div>
            <div class="pt-3 border-t"><span class="font-semibold">👤 Cliente:</span> {{ $address->customer->name ?? '-' }}</div>
        </div>

        <div class="mt-8 flex justify-end">
            <button 
                wire:click="$set('showViewModal', false)" 
                class="px-4 py-2 bg-blue-500 text-white rounded-lg shadow hover:bg-blue-700 transition">
                Fechar
            </button>
        </div>
    </div>
</div>
