@if ($showViewModal ?? true)
<div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="p-8 max-w-2xl mx-auto bg-white rounded-2xl shadow-lg border border-gray-200 text-center relative z-50" wire:click.stop>
        <h2 class="text-3xl font-bold text-gray-800 mb-6 flex items-center gap-2">
            Detalhes do Cliente
        </h2>

        <div class="space-y-4 text-gray-700 text-left">
            <div><span class="font-semibold text-gray-900">👤 Nome:</span> {{ $customer->name }}</div>
            <div><span class="font-semibold text-gray-900">📧 Email:</span> {{ $customer->email }}</div>
            <div><span class="font-semibold text-gray-900">📞 Telefone:</span> {{ $customer->phone ?? 'Não informado' }}</div>

            <div class="border-t pt-4">
                <span class="font-semibold text-gray-900 block mb-2">🏠 Endereços:</span>
                @forelse ($customer->addresses as $address)
                <div class="text-sm mb-1">
                    {{ $address->street }}, {{ $address->number }} - {{ $address->city }} (CEP: {{ $address->cep }})
                </div>
                @empty
                <p class="text-gray-400 text-sm italic">Nenhum endereço cadastrado.</p>
                @endforelse
            </div>
        </div>

        <div class="mt-8 flex justify-end">
            <button
                wire:click="closeModal"
                class="px-4 py-2 bg-blue-500 text-white rounded-lg shadow hover:bg-blue-700 transition">
                Fechar
            </button>
        </div>
    </div>
</div>
@endif
