<?php

namespace App\Livewire\Customers;

use App\Models\Customer;
use App\Models\Address;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class Form extends Component
{
    public ?int $customerId = null;
    public ?Customer $customer = null;
    public $cpf;

    public ?string $name = null;
    public ?string $email = null;
    public ?string $phone = null;

    public ?string $street = null;
    public ?string $number = null;
    public ?string $neighborhood = null;
    public ?string $city = null;
    public ?string $state = null;
    public ?string $cep = null;

    public $debugViaCepResponse = null;

    public bool $lookupLoading = false;

    public function mount(?int $customerId = null): void
    {
        $this->customerId = $customerId;

        if ($customerId) {
            $customer = Customer::with('addresses')->find($customerId);
            if ($customer) {
                $this->customer = $customer;
                $this->name = $customer->name;
                $this->email = $customer->email;
                $this->phone = $customer->phone;
                $this->cpf = $customer->cpf ?? '';

                $addr = $customer->addresses()->first();
                $this->street = $addr->street ?? null;
                $this->number = $addr->number ?? null;
                $this->neighborhood = $addr->neighborhood ?? null;
                $this->city = $addr->city ?? null;
                $this->state = $addr->state ?? null;
                $this->cep = $addr->cep ?? null;
                return;
            }
        }

        $this->customer = null;
        $this->name = '';
        $this->email = '';
        $this->phone = '';
        $this->street = $this->number = $this->neighborhood = $this->city = $this->state = $this->cep = null;
    }

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:120',
            'email' => 'required|email|unique:customers,email,' . ($this->customer->id ?? 'NULL'),
            'phone' => 'nullable|string|max:20',
            'street' => 'nullable|string|max:150',
            'number' => 'nullable|string|max:50',
            'neighborhood' => 'nullable|string|max:150',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'cep' => 'nullable|string|max:20',
        ];
    }

    public function lookupCep(): void
    {
        $this->lookupLoading = true;
        $this->debugViaCepResponse = null;

        $raw = $this->cep ?? '';
        $digits = preg_replace('/\D/', '', $raw);

        if (strlen($digits) < 8) {
            $this->lookupLoading = false;
            $this->dispatch('notify', ['message' => 'CEP inválido. Informe 8 dígitos ou preencha manualmente.', 'type' => 'warning']);
            return;
        }

        $url = "https://viacep.com.br/ws/{$digits}/json/";

        try {
            $response = Http::timeout(6)->get($url);

            $this->debugViaCepResponse = $response->body();

            if ($response->failed()) {
                Log::warning('ViaCEP request failed', ['url' => $url, 'status' => $response->status()]);
                $this->lookupLoading = false;
                $this->dispatch('notify', ['message' => 'Erro ao buscar CEP (rede). Preencha manualmente.', 'type' => 'error']);
                return;
            }

            $json = $response->json();

            if (isset($json['erro']) && $json['erro'] === true) {
                $this->lookupLoading = false;
                $this->dispatch('notify', ['message' => 'CEP não encontrado. Preencha manualmente ou tente outro CEP.', 'type' => 'warning']);
                return;
            }

            $this->street = $json['logradouro'] ?? $this->street;
            $this->neighborhood = $json['bairro'] ?? $this->neighborhood;
            $this->city = $json['localidade'] ?? $this->city;
            $this->state = $json['uf'] ?? $this->state;
            $this->cep = substr($digits, 0, 5) . '-' . substr($digits, 5);

            $this->lookupLoading = false;
            $this->dispatch('notify', ['message' => 'Endereço preenchido pelo CEP.', 'type' => 'success']);
        } catch (\Throwable $e) {
            Log::error('lookupCep exception: ' . $e->getMessage(), ['url' => $url]);
            $this->lookupLoading = false;
            $this->dispatch('notify', ['message' => 'Erro ao buscar CEP. Preencha manualmente.', 'type' => 'error']);
        }
    }

    public function cancel(): void
    {
        $this->dispatch('close-form-modal');
    }

    public function save(): void
    {
        $this->validate();

        DB::transaction(function () {
            $data = [
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'cpf' => $this->cpf,

            ];

            if ($this->customer && $this->customer->exists) {
                $this->customer->update($data);
            } else {
                $this->customer = Customer::create($data);
            }

            $hasAnyAddress = collect([
                $this->street,
                $this->number,
                $this->neighborhood,
                $this->city,
                $this->state,
                $this->cep
            ])->filter()->isNotEmpty();

            if ($hasAnyAddress) {
                $addrData = [
                    'street' => $this->street,
                    'number' => $this->number,
                    'neighborhood' => $this->neighborhood,
                    'city' => $this->city,
                    'state' => $this->state,
                    'cep' => $this->cep,
                ];

                $existing = $this->customer->addresses()->first();

                if ($existing) {
                    $ownersCount = $existing->customers()->count();

                    if ($ownersCount <= 1) {
                        $existing->update($addrData);
                    } else {
                        $newAddress = Address::create($addrData);
                        $this->customer->addresses()->attach($newAddress->id);
                        $this->customer->addresses()->detach($existing->id);
                    }
                } else {
                    $new = Address::create($addrData);
                    $this->customer->addresses()->attach($new->id);
                }
            } else {
                if ($this->customer->addresses()->exists()) {
                    $this->customer->addresses()->detach();
                }
            }
        });

        $this->dispatch('customer-saved');
        $this->dispatch('close-form-modal');
    }

    public function render()
    {
        return view('livewire.customers.form');
    }
}
