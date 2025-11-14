<div 
    x-data="{ open: true }"
    x-show="open"
    x-on:close-form.window="open = false"
    class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-50"
>
    <div class="bg-white w-full max-w-lg rounded-lg shadow p-6 relative">
        <h2 class="text-xl font-bold mb-4">
            {{ $customer->id ? 'Editar Cliente' : 'Novo Cliente' }}
        </h2>

        <form wire:submit.prevent="save" class="space-y-4">
            {{-- Nome --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">Nome</label>
                <input type="text" wire:model.defer="name" class="w-full border rounded px-3 py-2 focus:ring focus:ring-blue-200">
                @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            {{-- Email --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" wire:model.defer="email" class="w-full border rounded px-3 py-2 focus:ring focus:ring-blue-200">
                @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            {{-- Telefone --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">Telefone</label>
                <input type="text" wire:model.defer="phone" class="w-full border rounded px-3 py-2 focus:ring focus:ring-blue-200">
                @error('phone') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            {{-- Endereços --}}
            @if($customer->id)
            <div class="pt-2 border-t">
                <h3 class="text-sm font-medium text-gray-700 mb-2">Endereços</h3>
                <ul class="text-sm text-gray-600 space-y-1">
                    @forelse ($customer->addresses as $address)
                        <li class="flex justify-between">
                            <span>{{ $address->street }}, {{ $address->number }} - {{ $address->city }}</span>
                            <button wire:click="$dispatch('openAddressForm', {{ $address->id }})" type="button" class="text-blue-500 hover:text-blue-700 text-xs">
                                Editar
                            </button>
                        </li>
                    @empty
                        <li class="text-gray-400 italic">Nenhum endereço cadastrado.</li>
                    @endforelse
                </ul>

                <button 
                    type="button"
                    wire:click="$dispatch('openAddressForm', null, {{ $customer->id }})"
                    class="mt-3 inline-flex items-center px-3 py-1.5 bg-green-500 text-white text-xs rounded-full hover:bg-green-600 transition"
                >
                    + Adicionar Endereço
                </button>
            </div>
            @endif

            {{-- Botões --}}
            <div class="flex justify-end space-x-2 mt-4">
                <button 
                    type="button" 
                    wire:click="$dispatch('close-form-modal')"
                    class="px-4 py-2 bg-gray-300 text-gray-700 text-xs font-semibold rounded-full hover:bg-gray-400 transition">

                    Cancelar
                
                </button>

                <button 
                    type="submit" 
                    class="px-4 py-2 bg-blue-500 text-white text-xs font-semibold rounded-full hover:bg-blue-700 transition"
                >
                    Salvar
                </button>
            </div>
        </form>

        {{-- Modal secundário para endereço --}}
        @if($showAddressForm ?? false)
            <livewire:addresses.form 
                :address="$selectedAddress ?? new \App\Models\Address()" 
                :customerId="$customer->id"
                wire:key="address-form-{{ $selectedAddress->id ?? 'new' }}"
            />
        @endif
    </div>
</div>
