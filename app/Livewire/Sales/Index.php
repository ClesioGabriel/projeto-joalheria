<?php

namespace App\Livewire\Sales;

use App\Models\Sale;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;

#[Layout('layouts.app')]
class Index extends Component
{
    use WithPagination;

    public bool $showFormModal = false;
    public bool $showViewModal = false;
    public ?Sale $selectedSale = null;

    public array $statusOptions = [];

    public function mount()
    {
        $this->statusOptions = Sale::statuses();
    }

    #[On('sale-saved')]
    #[On('close-form-modal')]
    public function closeFormModal(): void
    {
        $this->showFormModal = false;
        $this->selectedSale = null;
        $this->resetPage();
    }

    public function create(): void
    {
        $this->selectedSale = null;
        $this->showFormModal = true;
        $this->dispatch('set-sale', null);
    }

    public function edit(int $saleId): void
    {
        $sale = Sale::find($saleId);
        if (! $sale) {
            $this->dispatchBrowserEvent('notify', ['message' => 'Venda não encontrada', 'type' => 'error']);
            return;
        }
        $this->selectedSale = $sale;
        $this->showFormModal = true;
        $this->dispatch('set-sale', $sale);
    }

    public function view(int $saleId): void
    {
        $sale = Sale::with('items.product', 'customer')->find($saleId);
        if (! $sale) {
            $this->dispatchBrowserEvent('notify', ['message' => 'Venda não encontrada', 'type' => 'error']);
            return;
        }
        $this->selectedSale = $sale;
        $this->showViewModal = true;
    }

    #[On('close-view-modal')]
    public function closeViewModal(): void
    {
        $this->showViewModal = false;
        $this->selectedSale = null;
    }

    public function delete(int $saleId): void
    {
        $sale = Sale::find($saleId);
        if (! $sale) {
            $this->dispatchBrowserEvent('notify', ['message' => 'Venda não encontrada', 'type' => 'error']);
            return;
        }
        $sale->delete();
        $this->dispatchBrowserEvent('notify', ['message' => 'Venda excluída com sucesso!']);
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.sales.index', [
            'sales' => Sale::with('customer')->latest()->paginate(10),
            'statusOptions' => $this->statusOptions,
        ]);
    }
}
