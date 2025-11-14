<div 
    x-data="{ 
        open: true,
        cepError: null,
        async fetchAddress() {
            const cepInput = this.$refs.cep.value.replace(/\D/g, '');
            this.cepError = null;

            if (cepInput.length === 8) {
                try {
                    const response = await fetch(`https://viacep.com.br/ws/${cepInput}/json/`);
                    const data = await response.json();

                    if (data.erro) {
                        this.cepError = 'CEP não encontrado. Preencha os dados manualmente.';
                        return;
                    }

                    // Preenche automaticamente apenas os campos que vieram da API
                    if (data.logradouro) $wire.set('address.street', data.logradouro);
                    if (data.bairro) $wire.set('address.neighborhood', data.bairro);
                    if (data.localidade) $wire.set('address.city', data.localidade);
                    if (data.uf) $wire.set('address.state', data.uf);

                } catch (e) {
                    console.error('Erro ao buscar CEP:', e);
                    this.cepError = 'Erro ao buscar CEP. Tente novamente.';
                }
            }
        }
    }"
    x-show="open"
    x-on:close-address-form.window="open = false"
    class="fixed inset-0 flex items-center justify-center z-[60] bg-black bg-opacity-60"
>
    <div class="bg-white w-full max-w-lg rounded-xl shadow-lg p-6">
        <h2 class="text-xl font-bold mb-4 text-gray-800">
            {{ $address->id ? 'Editar Endereço' : 'Novo Endereço' }}
        </h2>

        <form wire:submit.prevent="save" class="space-y-4">

            {{-- CEP --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">CEP</label>
                <input 
                    type="text"
                    x-ref="cep"
                    x-on:blur="fetchAddress()"
                    wire:model.defer="address.cep"
                    placeholder="Digite o CEP (ex: 30140-071)"
                    class="w-full border rounded px-3 py-2"
                >
                @error('address.cep') 
                    <span class="text-red-500 text-sm">{{ $message }}</span> 
                @enderror
                <template x-if="cepError">
                    <p class="text-yellow-600 text-sm mt-1" x-text="cepError"></p>
                </template>
            </div>

            {{-- Rua --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">Rua</label>
                <input type="text" wire:model.defer="address.street" class="w-full border rounded px-3 py-2">
                @error('address.street') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            {{-- Número e Complemento --}}
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Número</label>
                    <input type="text" wire:model.defer="address.number" class="w-full border rounded px-3 py-2">
                    @error('address.number') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Complemento</label>
                    <input type="text" wire:model.defer="address.complement" class="w-full border rounded px-3 py-2">
                    @error('address.complement') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            {{-- Bairro --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">Bairro</label>
                <input type="text" wire:model.defer="address.neighborhood" class="w-full border rounded px-3 py-2">
                @error('address.neighborhood') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            {{-- Cidade e Estado --}}
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Cidade</label>
                    <input type="text" wire:model.defer="address.city" class="w-full border rounded px-3 py-2">
                    @error('address.city') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Estado</label>
                    <input type="text" wire:model.defer="address.state" maxlength="2" class="w-full border rounded px-3 py-2 uppercase">
                    @error('address.state') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            {{-- Botões --}}
            <div class="flex justify-end space-x-2 mt-4">
                <button 
                    type="button" 
                    x-on:click="open = false; $dispatch('close-address-form')"
                    class="px-4 py-2 bg-red-500 text-white rounded-full text-sm hover:bg-red-700 transition"
                >
                    Cancelar
                </button>

                <button 
                    type="submit" 
                    class="px-4 py-2 bg-blue-500 text-white rounded-full text-sm hover:bg-blue-700 transition"
                >
                    Salvar
                </button>
            </div>
        </form>
    </div>
</div>
