{{-- resources/views/customer_orders/edit.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Estado del Pedido #') }}{{ $customerOrder->id }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('customer-orders.update', $customerOrder) }}" method="POST">
                        @csrf
                        @method('PUT') {{-- Importante para indicar que es una solicitud PUT --}}

                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-3">{{ __('Información del Pedido') }}</h3>
                            <p><strong>Cliente:</strong> {{ $customerOrder->client->user->name ?? 'N/A' }} ({{ $customerOrder->client->user->email ?? 'N/A' }})</p>
                            <p><strong>Monto Total:</strong> ${{ number_format($customerOrder->total_amount, 2) }}</p>
                            <p><strong>Fecha del Pedido:</strong> {{ $customerOrder->created_at->format('d/m/Y H:i') }}</p>
                        </div>

                        <div class="mb-4">
                            <x-input-label for="status" :value="__('Estado del Pedido')" />
                            <select id="status" name="status" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="pending" {{ old('status', $customerOrder->status) == 'pending' ? 'selected' : '' }}>Pendiente</option>
                                <option value="processing" {{ old('status', $customerOrder->status) == 'processing' ? 'selected' : '' }}>En Proceso</option>
                                <option value="completed" {{ old('status', $customerOrder->status) == 'completed' ? 'selected' : '' }}>Completado</option>
                                <option value="cancelled" {{ old('status', $customerOrder->status) == 'cancelled' ? 'selected' : '' }}>Cancelado</option>
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="shipping_address" :value="__('Dirección de Envío')" />
                            <textarea id="shipping_address" name="shipping_address" rows="2" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('shipping_address', $customerOrder->shipping_address) }}</textarea>
                            <x-input-error :messages="$errors->get('shipping_address')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('customer-orders.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500 active:bg-gray-700 focus:outline-none focus:border-gray-700 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150 mr-3">
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
