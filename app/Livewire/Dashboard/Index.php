<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\Customer;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Index extends Component
{
    public function render()
    {
        $totalCustomers = Customer::count();
        $totalSales = Sale::count();
        $totalRevenue = Sale::sum('total_amount');

        $topCustomers = Customer::select('customers.name', DB::raw('SUM(sales.total_amount) as total'))
            ->join('sales', 'customers.id', '=', 'sales.customer_id')
            ->groupBy('customers.id', 'customers.name')
            ->orderByDesc('total')
            ->take(5)
            ->get();

        $topProducts = SaleItem::select('products.name', DB::raw('SUM(sale_items.quantity) as total_qty'))
            ->join('products', 'products.id', '=', 'sale_items.product_id')
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_qty')
            ->take(5)
            ->get();

        $recentSales = Sale::with('customer')->latest()->take(10)->get();

        return view('livewire.dashboard.index', compact(
            'totalCustomers',
            'totalSales',
            'totalRevenue',
            'topCustomers',
            'topProducts',
            'recentSales'
        ));
    }
}
