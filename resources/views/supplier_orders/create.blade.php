{{-- resources/views/supplier_orders/create.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Registrar Nuevo Pedido a Proveedor') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('supplier-orders.store') }}" method="POST">
                        @csrf

                        <h4 class="font-semibold text-lg mb-4">{{ __('Datos del Proveedor') }}</h4>
                        <div class="mb-4">
                            <x-input-label for="supplier_id" :value="__('Seleccionar Proveedor')" />
                            <select id="supplier_id" name="supplier_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="">-- Seleccione un proveedor --</option>
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->name }} ({{ $supplier->contact_person ?? 'N/A' }})
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('supplier_id')" class="mt-2" />
                        </div>

                        <h4 class="font-semibold text-lg mt-6 mb-4">{{ __('Detalles de los Productos a Pedir') }}</h4>
                        <div id="product-items-container">
                            {{-- Aquí se agregarán dinámicamente los campos para los productos --}}
                            <div class="product-item border p-4 rounded-md mb-4 bg-gray-50">
                                <div class="mb-3">
                                    <x-input-label for="product_ids_0" :value="__('Producto')" />
                                    <select id="product_ids_0" name="product_ids[]" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm product-select" required>
                                        <option value="">-- Seleccione un producto --</option>
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}" data-price="{{ $product->price }}" {{ (old('product_ids.0') == $product->id) ? 'selected' : '' }}>
                                                {{ $product->name }} (SKU: {{ $product->sku ?? 'N/A' }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('product_ids.0')" class="mt-2" />
                                </div>
                                <div class="mb-3">
                                    <x-input-label for="quantities_0" :value="__('Cantidad')" />
                                    <x-text-input id="quantities_0" class="block mt-1 w-full quantity-input" type="number" name="quantities[]" min="1" :value="old('quantities.0', 1)" required />
                                    <x-input-error :messages="$errors->get('quantities.0')" class="mt-2" />
                                </div>
                                <div class="mb-3">
                                    <x-input-label for="cost_at_order_0" :value="__('Costo Unitario')" />
                                    <x-text-input id="cost_at_order_0" class="block mt-1 w-full cost-input" type="number" step="0.01" name="cost_at_order[]" min="0.01" :value="old('cost_at_order.0', 0.01)" required />
                                    <x-input-error :messages="$errors->get('cost_at_order.0')" class="mt-2" />
                                </div>
                                <div class="flex justify-end">
                                    <button type="button" class="text-red-500 hover:text-red-700 remove-product-item">Eliminar Producto</button>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end mb-6">
                            <button type="button" id="add-product-item" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                                {{ __('Añadir Otro Producto') }}
                            </button>
                        </div>

                        <h4 class="font-semibold text-lg mt-6 mb-4">{{ __('Información del Pedido') }}</h4>
                        <div class="mb-4">
                            <x-input-label for="order_date" :value="__('Fecha del Pedido')" />
                            <x-text-input id="order_date" class="block mt-1 w-full" type="date" name="order_date" :value="old('order_date', date('Y-m-d'))" />
                            <x-input-error :messages="$errors->get('order_date')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="expected_delivery_date" :value="__('Fecha de Entrega Estimada')" />
                            <x-text-input id="expected_delivery_date" class="block mt-1 w-full" type="date" name="expected_delivery_date" :value="old('expected_delivery_date')" />
                            <x-input-error :messages="$errors->get('expected_delivery_date')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="status" :value="__('Estado del Pedido')" />
                            <select id="status" name="status" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pendiente</option>
                                <option value="ordered" {{ old('status') == 'ordered' ? 'selected' : '' }}>Pedido</option>
                                <option value="received" {{ old('status') == 'received' ? 'selected' : '' }}>Recibido</option>
                                <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelado</option>
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('supplier-orders.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500 active:bg-gray-700 focus:outline-none focus:border-gray-700 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150 mr-3">
                                {{ __('Cancelar') }}
                            </a>
                            <x-primary-button>
                                {{ __('Registrar Pedido') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Script para añadir/eliminar productos dinámicamente --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let itemCounter = 0; // Para dar IDs únicos a los nuevos ítems

            document.getElementById('add-product-item').addEventListener('click', function () {
                itemCounter++;
                const container = document.getElementById('product-items-container');
                const newItem = document.createElement('div');
                newItem.classList.add('product-item', 'border', 'p-4', 'rounded-md', 'mb-4', 'bg-gray-50');

                newItem.innerHTML = `
                    <div class="mb-3">
                        <x-input-label for="product_ids_${itemCounter}" :value="__('Producto')" />
                        <select id="product_ids_${itemCounter}" name="product_ids[]" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm product-select" required>
                            <option value="">-- Seleccione un producto --</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}" data-price="{{ $product->price }}">
                                    {{ $product->name }} (SKU: {{ $product->sku ?? 'N/A' }})
                                </option>
                            @endforeach
                        </select>
                        @error('product_ids.${itemCounter}')<p class="text-sm text-red-600 mt-2">{{ $message }}</p>@enderror
                    </div>
                    <div class="mb-3">
                        <x-input-label for="quantities_${itemCounter}" :value="__('Cantidad')" />
                        <x-text-input id="quantities_${itemCounter}" class="block mt-1 w-full quantity-input" type="number" name="quantities[]" min="1" value="1" required />
                        @error('quantities.${itemCounter}')<p class="text-sm text-red-600 mt-2">{{ $message }}</p>@enderror
                    </div>
                    <div class="mb-3">
                        <x-input-label for="cost_at_order_${itemCounter}" :value="__('Costo Unitario')" />
                        <x-text-input id="cost_at_order_${itemCounter}" class="block mt-1 w-full cost-input" type="number" step="0.01" name="cost_at_order[]" min="0.01" value="0.01" required />
                        @error('cost_at_order.${itemCounter}')<p class="text-sm text-red-600 mt-2">{{ $message }}</p>@enderror
                    </div>
                    <div class="flex justify-end">
                        <button type="button" class="text-red-500 hover:text-red-700 remove-product-item">Eliminar Producto</button>
                    </div>
                `;
                container.appendChild(newItem);
            });

            document.getElementById('product-items-container').addEventListener('click', function (event) {
                if (event.target.classList.contains('remove-product-item')) {
                    if (document.querySelectorAll('.product-item').length > 1) {
                        event.target.closest('.product-item').remove();
                    } else {
                        alert('Un pedido a proveedor debe tener al menos un producto.');
                    }
                }
            });
        });
    </script>
</x-app-layout>
