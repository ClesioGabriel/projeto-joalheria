<div class="container mx-auto p-6">
    <div class="w-full max-w-5xl mx-auto bg-white shadow-xl rounded-2xl overflow-hidden p-4">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold text-gray-800">Endereços</h2>
            <button
                wire:click="create"
                class="inline-flex items-center px-4 py-2 bg-blue-500 text-white text-xs font-semibold rounded-full hover:bg-blue-700 transition"
            >
                + Novo Endereço
            </button>
        </div>

        <table class="w-full text-sm text-gray-700">
            <thead class="bg-gray-100 text-xs uppercase text-gray-600">
                <tr>
                    <th class="px-5 py-3 text-center">ID</th>
                    <th class="px-5 py-3 text-center">Rua</th>
                    <th class="px-5 py-3 text-center">Cidade</th>
                    <th class="px-5 py-3 text-center">CEP</th>
                    <th class="px-5 py-3 text-center">Cliente</th>
                    <th class="px-5 py-3 text-center">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse ($addresses as $address)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-5 py-3 text-center">{{ $address->id }}</td>
                        <td class="px-5 py-3 text-center">{{ $address->street }}, {{ $address->number }}</td>
                        <td class="px-5 py-3 text-center">{{ $address->city }}</td>
                        <td class="px-5 py-3 text-center">{{ $address->cep }}</td>
                        <td class="px-5 py-3 text-center">{{ $address->customer->name ?? '-' }}</td>
                        <td class="px-5 py-3 text-center space-x-2">
                            <button wire:click="view({{ $address->id }})"
                                class="px-3 py-1.5 bg-blue-500 text-white text-xs rounded-full hover:bg-blue-700 transition">
                                Ver
                            </button>
                            <button wire:click="edit({{ $address->id }})"
                                class="px-3 py-1.5 bg-gray-500 text-white text-xs rounded-full hover:bg-gray-700 transition">
                                Editar
                            </button>
                            <button wire:click="delete({{ $address->id }})"
                                onclick="return confirm('Tem certeza que deseja excluir este endereço?')"
                                class="px-3 py-1.5 bg-red-600 text-white text-xs rounded-full hover:bg-red-800 transition">
                                Excluir
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-gray-500">Nenhum endereço encontrado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-4">
            {{ $addresses->links() }}
        </div>
    </div>

    {{-- Modal de formulário --}}
    @if($showFormModal)
        <livewire:addresses.form 
            :address="$selectedAddress ?? new \App\Models\Address()" 
            wire:key="form-{{ $selectedAddress->id ?? 'new' }}" 
        />
    @endif

    {{-- Modal de visualização --}}
    @if($showViewModal)
        <livewire:addresses.show 
            :address="$selectedAddress" 
            wire:key="show-{{ $selectedAddress->id }}"
        />
    @endif
</div>
