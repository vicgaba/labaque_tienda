{{-- resources/views/supplier_orders/edit.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Estado del Pedido a Proveedor #') }}{{ $supplierOrder->id }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('supplier-orders.update', $supplierOrder) }}" method="POST">
                        @csrf
                        @method('PUT') {{-- Importante para indicar que es una solicitud PUT --}}

                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-3">{{ __('Información del Pedido') }}</h3>
                            <p><strong>Proveedor:</strong> {{ $supplierOrder->supplier->name ?? 'N/A' }}</p>
                            <p><strong>Monto Total:</strong> ${{ number_format($supplierOrder->total_amount, 2) }}</p>
                            <p><strong>Fecha del Pedido:</strong> {{ $supplierOrder->order_date ? \Carbon\Carbon::parse($supplierOrder->order_date)->format('d/m/Y') : 'N/A' }}</p>
                        </div>

                        <div class="mb-4">
                            <x-input-label for="order_date" :value="__('Fecha del Pedido')" />
                            <x-text-input id="order_date" class="block mt-1 w-full" type="date" name="order_date" :value="old('order_date', $supplierOrder->order_date ? \Carbon\Carbon::parse($supplierOrder->order_date)->format('Y-m-d') : '')" />
                            <x-input-error :messages="$errors->get('order_date')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="expected_delivery_date" :value="__('Fecha de Entrega Estimada')" />
                            <x-text-input id="expected_delivery_date" class="block mt-1 w-full" type="date" name="expected_delivery_date" :value="old('expected_delivery_date', $supplierOrder->expected_delivery_date ? \Carbon\Carbon::parse($supplierOrder->expected_delivery_date)->format('Y-m-d') : '')" />
                            <x-input-error :messages="$errors->get('expected_delivery_date')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="status" :value="__('Estado del Pedido')" />
                            <select id="status" name="status" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="pending" {{ old('status', $supplierOrder->status) == 'pending' ? 'selected' : '' }}>Pendiente</option>
                                <option value="ordered" {{ old('status', $supplierOrder->status) == 'ordered' ? 'selected' : '' }}>Pedido</option>
                                <option value="received" {{ old('status', $supplierOrder->status) == 'received' ? 'selected' : '' }}>Recibido</option>
                                <option value="cancelled" {{ old('status', $supplierOrder->status) == 'cancelled' ? 'selected' : '' }}>Cancelado</option>
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('supplier-orders.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500 active:bg-gray-700 focus:outline-none focus:border-gray-700 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150 mr-3">
                                {{ __('Cancelar') }}
                            </a>
                            <x-primary-button>
                                {{ __('Actualizar Pedido') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
