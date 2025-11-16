<div
    x-data="{
        open: true,
        clientLoading: false,
        async lookupClientCep() {
            const cepEl = this.$refs.cep;
            if (!cepEl) return;
            const raw = (cepEl.value || '').replace(/\D/g, '');
            if (raw.length < 8) {
                // mensagem simples — você pode trocar por um toast se tiver um
                alert('Informe 8 dígitos do CEP.');
                return;
            }

            this.clientLoading = true;
            try {
                const res = await fetch('https://viacep.com.br/ws/' + raw + '/json/');
                if (!res.ok) {
                    alert('Erro ao consultar ViaCEP (client).');
                    this.clientLoading = false;
                    return;
                }
                const json = await res.json();
                if (json.erro) {
                    alert('CEP não encontrado (client).');
                    this.clientLoading = false;
                    return;
                }

                // Preenche os inputs via refs e dispara event input para Livewire sincronizar
                const map = {
                    street: json.logradouro || '',
                    neighborhood: json.bairro || '',
                    city: json.localidade || '',
                    state: json.uf || '',
                    cep: raw.slice(0,5) + '-' + raw.slice(5)
                };

                Object.entries(map).forEach(([k, v]) => {
                    const el = this.$refs[k];
                    if (el) {
                        el.value = v;
                        // dispatch input event so Livewire picks it up
                        el.dispatchEvent(new Event('input', { bubbles: true }));
                    }
                });

                // foco no número (opcional)
                if (this.$refs.number) this.$refs.number.focus();

                // opcional: mensagem
                // alert('Endereço preenchido (client).');
            } catch (err) {
                console.error('CEP client lookup error', err);
                alert('Erro no lookup do CEP (client). Veja console.');
            } finally {
                this.clientLoading = false;
            }
        }
    }"
    x-show="open"
    x-on:close-form-modal.window="open = false"
    class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-50"
>
    <div class="bg-white w-full max-w-lg rounded-lg shadow relative">
        {{-- Conteúdo rolável --}}
        <div style="max-height:80vh; overflow:auto; padding:1.25rem;">
            <h2 class="text-xl font-bold mb-4">
                {{ $customer ? 'Editar Cliente' : 'Novo Cliente' }}
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

                {{-- Rua --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700">Rua</label>
                    <input id="street" x-ref="street" type="text" wire:model.defer="street" class="w-full border rounded px-3 py-2 focus:ring focus:ring-blue-200">
                    @error('street') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                {{-- Número --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700">Número</label>
                    <input id="number" x-ref="number" type="text" wire:model.defer="number" class="w-full border rounded px-3 py-2 focus:ring focus:ring-blue-200">
                    @error('number') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                {{-- Bairro --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700">Bairro</label>
                    <input id="neighborhood" x-ref="neighborhood" type="text" wire:model.defer="neighborhood" class="w-full border rounded px-3 py-2 focus:ring focus:ring-blue-200">
                    @error('neighborhood') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                {{-- Cidade --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700">Cidade</label>
                    <input id="city" x-ref="city" type="text" wire:model.defer="city" class="w-full border rounded px-3 py-2 focus:ring focus:ring-blue-200">
                    @error('city') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                {{-- Estado --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700">Estado</label>
                    <input id="state" x-ref="state" type="text" wire:model.defer="state" class="w-full border rounded px-3 py-2 focus:ring focus:ring-blue-200">
                    @error('state') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                {{-- CEP com botão de lookup --}}
                <div class="flex items-start gap-3">
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700">CEP</label>
                        <input id="cep" x-ref="cep" type="text" wire:model.defer="cep" placeholder="00000-000 ou 00000000"
                            class="w-full border rounded px-3 py-2 focus:ring focus:ring-blue-200">
                        @error('cep') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="mt-6 flex flex-col gap-2">
                        {{-- client-side lookup button (bonitinho) --}}
                        <button
                            type="button"
                            x-bind:disabled="clientLoading"
                            x-on:click.prevent="lookupClientCep()"
                            class="inline-flex items-center px-3 py-2 bg-indigo-600 text-white rounded-md text-sm hover:bg-indigo-700 transition disabled:opacity-60 disabled:cursor-not-allowed"
                        >
                            <svg x-show="!clientLoading" class="h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>

                            <svg x-show="clientLoading" class="animate-spin h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                            </svg>

                            <span x-text="clientLoading ? 'Buscando...' : 'Pesquisar CEP'"></span>
                        </button>

                        {{-- fallback para quem preferir: mantém o botão antigo de pesquisa client (opcional) --}}
                        <button
                            type="button"
                            x-on:click.prevent="(async()=>{ await lookupClientCep(); })()"
                            class="inline-flex items-center px-3 py-2 bg-gray-600 text-white rounded-md text-sm hover:bg-gray-700 transition"
                            style="display:none"
                        >
                            Pesquisar (client)
                        </button>
                    </div>
                </div>

                {{-- debug: mostra a resposta bruta do viaCEP quando presente (somente para testes) --}}
                @if($debugViaCepResponse)
                    <pre class="mt-2 p-2 bg-gray-100 text-xs rounded">{{ $debugViaCepResponse }}</pre>
                @endif

            </form>
        </div>

        {{-- Rodapé fixo com botões --}}
        <div class="border-t bg-white p-4 flex justify-end space-x-2">
            <button
                type="button"
                wire:click="$parent.closeFormModal"
                onclick="window.dispatchEvent(new Event('close-form-modal'))"
                class="px-4 py-2 bg-gray-300 text-gray-700 text-xs font-semibold rounded-full hover:bg-gray-400 transition">
                Cancelar
            </button>

            {{-- botão submit do form: submete o form interno --}}
            <button
                type="button"
                onclick="this.closest('.relative').querySelector('form').dispatchEvent(new Event('submit', {cancelable: true, bubbles: true}))"
                class="px-4 py-2 bg-blue-500 text-white text-xs font-semibold rounded-full hover:bg-blue-700 transition">
                Salvar
            </button>
        </div>
    </div>
</div>
