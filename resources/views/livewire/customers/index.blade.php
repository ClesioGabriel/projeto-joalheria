<div class="container mx-auto p-6">

    <div class="mt-6 flex justify-center">
        <div class="w-full max-w-6xl bg-white shadow-xl rounded-2xl overflow-hidden p-6">

            {{-- TÍTULO + BOTÃO --}}
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-800">Lista de Clientes</h2>

                <button
                    wire:click="create"
                    class="inline-flex items-center px-4 py-2 bg-blue-500 text-white text-xs font-semibold rounded-full hover:bg-blue-700 transition">
                    Novo Cliente
                </button>
            </div>

            {{-- CAMPO DE BUSCA --}}
            <div class="mb-4">
                <input
                    type="text"
                    id="customer-search"
                    placeholder="Buscar clientes por nome, email, telefone..."
                    class="block w-full p-3 text-gray-900 border border-gray-300 rounded-lg bg-white text-base focus:ring-blue-500 focus:border-blue-500"
                    onkeyup="filterCustomers()"
                >
            </div>

            {{-- TABELA --}}
            <table class="w-full bg-white shadow-xl rounded-xl overflow-hidden">
                <thead class="bg-gray-100 text-xs uppercase text-gray-600">
                    <tr>
                        <th class="px-5 py-3 text-center">ID</th>
                        <th class="px-5 py-3 text-center">Nome</th>
                        <th class="px-5 py-3 text-center">Email</th>
                        <th class="px-5 py-3 text-center">Telefone</th>
                        <th class="px-5 py-3 text-center">Ações</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200">

                    @foreach ($customers as $customer)
                        <tr class="hover:bg-gray-50 transition customer-item"
                            data-name="{{ strtolower($customer->name) }}"
                            data-email="{{ strtolower($customer->email) }}"
                            data-phone="{{ strtolower($customer->phone ?? '') }}"
                        >
                            <td class="px-5 py-3 text-center">{{ $customer->id }}</td>

                            <td class="px-5 py-3 text-center">{{ $customer->name }}</td>

                            <td class="px-5 py-3 text-center">{{ $customer->email }}</td>

                            <td class="px-5 py-3 text-center">{{ $customer->phone ?? '-' }}</td>

                            <td class="px-5 py-3 text-center space-x-2">
                                <button wire:click="view({{ $customer->id }})"
                                        class="inline-flex items-center px-3 py-1.5 bg-green-500 text-white text-xs font-semibold rounded-full hover:bg-green-700 transition">
                                    Visualizar
                                </button>

                                <button wire:click="edit({{ $customer->id }})"
                                        class="inline-flex items-center px-3 py-1.5 bg-gray-500 text-white text-xs font-semibold rounded-full hover:bg-gray-700 transition">
                                    Editar
                                </button>

                                <button wire:click="delete({{ $customer->id }})"
                                        onclick="return confirm('Tem certeza que deseja deletar?')"
                                        class="inline-flex items-center px-3 py-1.5 bg-red-600 text-white text-xs font-semibold rounded-full hover:bg-red-800 transition">
                                    Excluir
                                </button>
                            </td>
                        </tr>
                    @endforeach

                </tbody>
            </table>

        </div>
    </div>

    {{-- PAGINAÇÃO --}}
    <div class="mt-6 max-w-6xl mx-auto bg-white p-4 rounded-2xl shadow">
        {{ $customers->links() }}

        @if($customers->isEmpty())
            <div class="text-center text-gray-500 py-4">
                Nenhum cliente encontrado.
            </div>
        @endif
    </div>

    {{-- MODAL FORM --}}
    @if($showFormModal)
        <livewire:customers.form
            :customer-id="$selectedCustomer->id ?? null"
            wire:key="form-{{ $selectedCustomer->id ?? 'new' }}"
        />
    @endif

    {{-- MODAL VIEW --}}
    @if($showViewModal && $selectedCustomer)
        <livewire:customers.show
            :customer="$selectedCustomer"
            wire:key="show-{{ $selectedCustomer->id }}"
        />
    @endif

</div>

{{-- SCRIPT DE BUSCA --}}
@once
    <script>
        function filterCustomers() {
            const search = document.getElementById('customer-search').value.toLowerCase();
            const items = document.querySelectorAll('.customer-item');

            items.forEach(item => {
                const name = item.dataset.name || '';
                const email = item.dataset.email || '';
                const phone = item.dataset.phone || '';

                const match =
                    name.includes(search) ||
                    email.includes(search) ||
                    phone.includes(search);

                item.style.display = match ? '' : 'none';
            });
        }
    </script>
@endonce
