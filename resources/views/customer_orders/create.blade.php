{{-- resources/views/customer_orders/create.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Registrar Nueva Venta') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('customer-orders.store') }}" method="POST">
                        @csrf

                        <h4 class="font-semibold text-lg mb-4">{{ __('Datos del Cliente') }}</h4>
                        <div class="mb-4">
                            <x-input-label for="client_id" :value="__('Seleccionar Cliente')" />
                            <select id="client_id" name="client_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="">-- Seleccione un cliente --</option>
                                @foreach ($clients as $client)
                                    <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                        {{ $client->user->name }} ({{ $client->user->email }})
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('client_id')" class="mt-2" />
                        </div>

                        <h4 class="font-semibold text-lg mt-6 mb-4">{{ __('Detalles de los Productos') }}</h4>
                        <div id="product-items-container">
                            {{-- Aquí se agregarán dinámicamente los campos para los productos --}}
                            <div class="product-item border p-4 rounded-md mb-4 bg-gray-50">
                                <div class="mb-3">
                                    <x-input-label for="product_ids_0" :value="__('Producto')" />
                                    <select id="product_ids_0" name="product_ids[]" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm product-select" required>
                                        <option value="">-- Seleccione un producto --</option>
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}" data-price="{{ $product->price }}" data-stock="{{ $product->stock }}" {{ (old('product_ids.0') == $product->id) ? 'selected' : '' }}>
                                                {{ $product->name }} (SKU: {{ $product->sku ?? 'N/A' }}, Stock: {{ $product->stock }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('product_ids.0')" class="mt-2" />
                                </div>
                                <div class="mb-3">
                                    <x-input-label for="quantities_0" :value="__('Cantidad')" />
                                    <x-text-input id="quantities_0" class="block mt-1 w-full quantity-input" type="number" name="quantities[]" min="1" :value="old('quantities.0', 1)" required />
                                    <x-input-error :messages="$errors->get('quantities.0')" class="mt-2" />
                                    <span class="text-sm text-gray-500" id="stock-info-0">Stock disponible: --</span>
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
                            <x-input-label for="status" :value="__('Estado del Pedido')" />
                            <select id="status" name="status" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pendiente</option>
                                <option value="processing" {{ old('status') == 'processing' ? 'selected' : '' }}>En Proceso</option>
                                <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completado</option>
                                <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelado</option>
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="shipping_address" :value="__('Dirección de Envío (si aplica)')" />
                            <textarea id="shipping_address" name="shipping_address" rows="2" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('shipping_address') }}</textarea>
                            <x-input-error :messages="$errors->get('shipping_address')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('customer-orders.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500 active:bg-gray-700 focus:outline-none focus:border-gray-700 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150 mr-3">
                                {{ __('Cancelar') }}
                            </a>
                            <x-primary-button>
                                {{ __('Registrar Venta') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Script para añadir/eliminar productos dinámicamente y mostrar stock --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let itemCounter = 0; // Para dar IDs únicos a los nuevos ítems

            function updateStockInfo(selectElement) {
                const selectedOption = selectElement.options[selectElement.selectedIndex];
                const stock = selectedOption.dataset.stock;
                const itemId = selectElement.id.split('_')[2]; // Obtener el índice del item
                const stockInfoSpan = document.getElementById('stock-info-' + itemId);
                if (stockInfoSpan) {
                    stockInfoSpan.textContent = 'Stock disponible: ' + (stock !== undefined ? stock : '--');
                }
            }

            // Actualizar stock info al cargar la página para el primer ítem
            const initialSelect = document.getElementById('product_ids_0');
            if (initialSelect) {
                updateStockInfo(initialSelect);
            }

            // Listener para actualizar stock info cuando cambia la selección de producto
            document.getElementById('product-items-container').addEventListener('change', function(event) {
                if (event.target.classList.contains('product-select')) {
                    updateStockInfo(event.target);
                }
            });


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
                                <option value="{{ $product->id }}" data-price="{{ $product->price }}" data-stock="{{ $product->stock }}">
                                    {{ $product->name }} (SKU: {{ $product->sku ?? 'N/A' }}, Stock: {{ $product->stock }})
                                </option>
                            @endforeach
                        </select>
                        @error('product_ids.${itemCounter}')<p class="text-sm text-red-600 mt-2">{{ $message }}</p>@enderror
                    </div>
                    <div class="mb-3">
                        <x-input-label for="quantities_${itemCounter}" :value="__('Cantidad')" />
                        <x-text-input id="quantities_${itemCounter}" class="block mt-1 w-full quantity-input" type="number" name="quantities[]" min="1" value="1" required />
                        @error('quantities.${itemCounter}')<p class="text-sm text-red-600 mt-2">{{ $message }}</p>@enderror
                        <span class="text-sm text-gray-500" id="stock-info-${itemCounter}">Stock disponible: --</span>
                    </div>
                    <div class="flex justify-end">
                        <button type="button" class="text-red-500 hover:text-red-700 remove-product-item">Eliminar Producto</button>
                    </div>
                `;
                container.appendChild(newItem);

                // Re-inicializar el listener de stock para el nuevo select
                const newSelect = newItem.querySelector('.product-select');
                if (newSelect) {
                    updateStockInfo(newSelect); // Actualizar info de stock al añadir el nuevo ítem
                }
            });

            document.getElementById('product-items-container').addEventListener('click', function (event) {
                if (event.target.classList.contains('remove-product-item')) {
                    if (document.querySelectorAll('.product-item').length > 1) {
                        event.target.closest('.product-item').remove();
                    } else {
                        alert('Una venta debe tener al menos un producto.');
                    }
                }
            });
        });
    </script>
</x-app-layout>
