<?php

namespace App\Livewire\Addresses;

use App\Models\Address;
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
    public ?Address $selectedAddress = null;
    public ?int $customerFilter = null;
    public string $search = '';

    protected $queryString = ['search'];

    #[On('address-saved')]
    #[On('close-address-form')]
    public function closeFormModal(): void
    {
        $this->reset(['showFormModal', 'selectedAddress']);
        $this->resetPage();
    }

    public function create(?int $customerId = null): void
    {
        $this->selectedAddress = null;
        $this->customerFilter = $customerId;
        $this->showFormModal = true;

        // avisa o Form (componente filho) para setar o customer/id
        $this->dispatch('set-customer-for-address', $customerId);
        $this->dispatch('set-address', null);
    }

    public function edit(Address $address): void
    {
        $this->selectedAddress = $address;
        $this->showFormModal = true;
        $this->dispatch('set-address', $address);
    }

    public function view(Address $address): void
    {
        $this->selectedAddress = $address->load('customers');
        $this->showViewModal = true;
    }

    #[On('close-address-view')]
    public function closeViewModal(): void
    {
        $this->reset(['showViewModal', 'selectedAddress']);
    }

    public function delete(Address $address): void
    {
        // when deleting, pivot entries are removed automatically (cascade) if migration setup
        $address->delete();
        $this->dispatch('notify', ['message' => 'Endereço excluído com sucesso!']);
        $this->resetPage();
    }

    public function render()
    {
        $query = Address::query()->with('customers');

        if ($this->customerFilter) {
            $query->whereHas('customers', function ($q) {
                $q->where('id', $this->customerFilter);
            });
        }

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('street', 'like', "%{$this->search}%")
                  ->orWhere('city', 'like', "%{$this->search}%")
                  ->orWhere('cep', 'like', "%{$this->search}%");
            });
        }

        return view('livewire.addresses.index', [
            'addresses' => $query->latest()->paginate(10),
        ]);
    }
}
