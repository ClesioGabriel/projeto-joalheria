{{-- 
  REMOVIDO: x-data, x-show e x-on:close-form.window.
  A exibição deste modal é controlada pelo @if($showFormModal) no index.blade.php.
--}}
<div 
    class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-50"
    style="overflow-y: auto;"
>
    <div class="bg-white w-full max-w-lg rounded-lg shadow p-6 my-8">
        <h2 class="text-xl font-bold mb-4">
            Informações do Produto
        </h2>

        @if($serial_number)
            <div class="mb-4 p-2 bg-gray-100 rounded">
                <span class="font-semibold">Nº de Série:</span> {{ $serial_number }}
            </div>
        @endif

        <form wire:submit.prevent="save" class="space-y-4">
            {{-- Campos existentes --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">Nome</label>
                <input type="text" wire:model.defer="name" class="w-full border rounded px-3 py-2">
                @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            {{-- --- ADICIONADO: Campos Faltantes (Stock e Type) --- --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tipo de Produto</label>
                    <select wire:model.defer="product_type" class="w-full border rounded px-3 py-2 bg-white">
                        <option value="{{ \App\Models\Product::TYPE_FINISHED }}">Produto Acabado</option>
                        <option value="{{ \App\Models\Product::TYPE_RAW }}">Matéria-Prima</option>
                    </select>
                    @error('product_type') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Estoque</label>
                    <input type="number" wire:model.defer="stock" step="1" class="w-full border rounded px-3 py-2">
                    @error('stock') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>
            {{-- -------------------------------------------------- --}}

            <div>
                <label class="block text-sm font-medium text-gray-700">Preço</label>
                <input type="number" wire:model.defer="price" step="0.01" class="w-full border rounded px-3 py-2">
                @error('price') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Descrição</label>
                <textarea wire:model.defer="description" class="w-full border rounded px-3 py-2"></textarea>
                @error('description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            {{-- --- NOVOS CAMPOS (Joia) --- --}}
            <hr>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Metal</label>
                    <input type="text" wire:model.defer="metal" placeholder="Ex: Ouro 18k" class="w-full border rounded px-3 py-2">
                    @error('metal') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Peso (gramas)</label>
                    <input type="number" wire:model.defer="weight" step="0.01" placeholder="Ex: 5.25" class="w-full border rounded px-3 py-2">
                    @error('weight') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tipo da Pedra</label>
                    <input type="text" wire:model.defer="stone_type" placeholder="Ex: Diamante" class="w-full border rounded px-3 py-2">
                    @error('stone_type') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tamanho da Pedra</label>
                    <input type="text" wire:model.defer="stone_size" placeholder="Ex: 0.5ct" class="w-full border rounded px-3 py-2">
                    @error('stone_size') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Localização Física</label>
                <input type="text" wire:model.defer="location" placeholder="Ex: Vitrine 1, Cofre" class="w-full border rounded px-3 py-2">
                @error('location') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Foto do Produto</label>
                <input type="file" wire:model="photo" class="w-full border rounded px-3 py-2">
                @error('photo') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

                @if ($photo)
                    <div class="mt-2">
                        <img src="{{ $photo->temporaryUrl() }}" class="w-32 h-32 object-cover rounded">
                    </div>
                @elseif ($existing_photo_path)
                    <div class="mt-2">
                        <img src="{{ asset('storage/' . $existing_photo_path) }}" class="w-32 h-32 object-cover rounded">
                    </div>
                @endif
            </div>

            <div class="flex justify-end space-x-2 mt-4">
                {{-- --- BOTÃO CANCELAR CORRIGIDO --- --}}
                <button 
                    type="button" 
                    {{-- Dispara o evento que o Index.php espera --}}
                    wire:click="$dispatch('close-form-modal')"
                    wire:loading.attr="disabled" {{-- Desabilita durante o 'save' --}}
                    class="inline-flex items-center px-4 py-2 bg-red-500 text-white text-xs font-semibold rounded-full hover:bg-red-700 transition disabled:opacity-50"
                >
                    Cancelar
                </button>
                
                {{-- --- BOTÃO SALVAR CORRIGIDO --- --}}
                <button 
                    type="submit" 
                    wire:loading.attr="disabled" {{-- Desabilita durante o submit --}}
                    wire:target="save"         {{-- Target é a ação 'save' --}}
                    class="inline-flex items-center px-4 py-2 bg-blue-500 text-white text-xs font-semibold rounded-full hover:bg-blue-700 transition disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    {{-- Mostra 'Salvar' por padrão --}}
                    <span wire:loading.remove wire:target="save">
                        Salvar
                    </span>
                    {{-- Mostra 'Salvando...' durante o loading --}}
                    <span wire:loading wire:target="save">
                        Salvando...
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>