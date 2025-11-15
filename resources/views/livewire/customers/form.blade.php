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

            <!-- ENDEREÇO DO CLIENTE -->

            {{-- Rua --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">Rua</label>
                <input type="text" wire:model.defer="street" class="w-full border rounded px-3 py-2 focus:ring focus:ring-blue-200">
                @error('street') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            {{-- Número --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">Número</label>
                <input type="text" wire:model.defer="number" class="w-full border rounded px-3 py-2 focus:ring focus:ring-blue-200">
                @error('number') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            {{-- Bairro --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">Bairro</label>
                <input type="text" wire:model.defer="neighborhood" class="w-full border rounded px-3 py-2 focus:ring focus:ring-blue-200">
                @error('neighborhood') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            {{-- Cidade --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">Cidade</label>
                <input type="text" wire:model.defer="city" class="w-full border rounded px-3 py-2 focus:ring focus:ring-blue-200">
                @error('city') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            {{-- Estado --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">Estado</label>
                <input type="text" wire:model.defer="state" class="w-full border rounded px-3 py-2 focus:ring focus:ring-blue-200">
                @error('state') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            {{-- CEP --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">CEP</label>
                <input type="text" wire:model.defer="cep" class="w-full border rounded px-3 py-2 focus:ring focus:ring-blue-200">
                @error('cep') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>


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
                    class="px-4 py-2 bg-blue-500 text-white text-xs font-semibold rounded-full hover:bg-blue-700 transition">
                    Salvar
                </button>
            </div>
        </form>

        {{-- Modal secundário de endereço --}}
        @if($showAddressForm ?? false)
            <livewire:addresses.form 
                :address="$selectedAddress ?? new \App\Models\Address()" 
                :customerId="$customer->id"
                wire:key="address-form-{{ $selectedAddress->id ?? 'new' }}"
            />
        @endif
    </div>
</div>
