<?php

namespace App\Livewire\Customers;

use App\Models\Customer;
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
    public ?Customer $selectedCustomer = null;

    public string $search = '';

    protected $queryString = ['search'];

    // quando o formulário salvar ou pedir para fechar, esse método será chamado
    #[On('customer-saved')]
    #[On('close-form-modal')]
    public function closeFormModal(): void
    {
        $this->showFormModal = false;
        $this->selectedCustomer = null;
        $this->resetPage();
    }

    public function create(): void
    {
        // garante que exista um modelo para o formulário — O PAI cria o model
        $this->selectedCustomer = new Customer();
        $this->showFormModal = true;

        // opcional: notificar o form via evento (caso o form escute 'set-customer')
        $this->dispatch('set-customer', $this->selectedCustomer);
    }

    public function edit(Customer $customer): void
    {
        $this->selectedCustomer = $customer;
        $this->showFormModal = true;

        // notifica o componente filho para carregar os dados (se o filho usar listener)
        $this->dispatch('set-customer', $customer);
    }

    public function view(int $customerId): void
    {
        $customer = Customer::with('addresses')->find($customerId);
        if (!$customer) {
            $this->dispatch('notify', ['message' => 'Cliente não encontrado', 'type' => 'error']);
            return;
        }

        $this->selectedCustomer = $customer;
        $this->showViewModal = true;
    }

    public function delete(int $customerId): void
    {
        $customer = Customer::find($customerId);
        if (!$customer) {
            $this->dispatch('notify', ['message' => 'Cliente não encontrado', 'type' => 'error']);
            return;
        }

        $customer->delete();
        $this->dispatch('notify', ['message' => 'Cliente excluído com sucesso!']);
        $this->resetPage();
    }

    #[On('close-view-modal')]
    public function closeViewModal(): void
    {
        $this->reset(['showViewModal', 'selectedCustomer']);
    }

    public function render()
    {
        $query = Customer::query();

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%")
                  ->orWhere('phone', 'like', "%{$this->search}%");
            });
        }

        return view('livewire.customers.index', [
            'customers' => $query->latest()->paginate(10),
        ]);
    }
}
