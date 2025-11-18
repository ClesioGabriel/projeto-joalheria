<div class="p-8 space-y-8">

    <!-- KPIs -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-2xl shadow text-center">
            <h3 class="text-gray-600 font-semibold">Clientes</h3>
            <p class="text-3xl font-bold text-blue-600">{{ $totalCustomers }}</p>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow text-center">
            <h3 class="text-gray-600 font-semibold">Vendas</h3>
            <p class="text-3xl font-bold text-green-600">{{ $totalSales }}</p>
        </div>
        
        <div class="bg-white p-6 rounded-2xl shadow text-center">
            <h3 class="text-gray-600 font-semibold">Total Arrecadado</h3>
            <p class="text-3xl font-bold text-indigo-600">
                R$ {{ number_format($totalRevenue, 2, ',', '.') }}
            </p>
        </div>
    </div>

    <!-- Gráficos -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white p-6 rounded-2xl shadow">
            <h3 class="text-lg font-semibold mb-4">Top 5 Clientes</h3>
            <canvas id="topCustomersChart"></canvas>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow">
            <h3 class="text-lg font-semibold mb-4">Produtos Mais Vendidos</h3>
            <canvas id="topProductsChart"></canvas>
        </div>
    </div>

    <!-- Tabela de Vendas Recentes -->
    <div class="bg-white p-6 rounded-2xl shadow">
        <h3 class="text-lg font-semibold mb-4">Últimas Vendas</h3>
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-100 text-gray-600 text-sm">
                    <th class="p-3">Cliente</th>
                    <th class="p-3">Data</th>
                    <th class="p-3">Valor</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentSales as $sale)
                <tr class="border-b hover:bg-gray-50">
                    <td class="p-3">{{ $sale->customer->name ?? '—' }}</td>
                    <td class="p-3">{{ \Carbon\Carbon::parse($sale->date)->format('d/m/Y') }}</td>
                    <td class="p-3 font-semibold text-blue-600">
                        R$ {{ number_format($sale->total_amount, 2, ',', '.') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', (event) => {
        
        // Gráfico 1: Clientes
        const ctxCustomersEl = document.getElementById('topCustomersChart');
        
        if (ctxCustomersEl) {
            const ctxCustomers = ctxCustomersEl.getContext('2d');
            new Chart(ctxCustomers, {
                type: 'bar',
                data: {
                    // CORRETO: Use um único @. 
                    // Ignore o "erro" de sintaxe do seu editor.
                    labels: @json($topCustomers->pluck('name')),
                    datasets: [{
                        label: 'Total Gasto (R$)',
                        data: @json($topCustomers->pluck('total')),
                        backgroundColor: '#3b82f6'
                    }]
                }
            });
        }

        // Gráfico 2: Produtos
        const ctxProductsEl = document.getElementById('topProductsChart');
        
        if (ctxProductsEl) {
            const ctxProducts = ctxProductsEl.getContext('2d');
            new Chart(ctxProducts, {
                type: 'pie',
                data: {
                    // CORRETO: Use um único @.
                    labels: @json($topProducts->pluck('name')),
                    datasets: [{
                        data: @json($topProducts->pluck('total_qty')),
                        backgroundColor: ['#6366f1','#22c55e','#f97316','#ef4444','#14b8a6']
                    }]
                }
            });
        }
        
    });
</script>
@endpush