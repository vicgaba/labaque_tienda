{{-- resources/views/supplier_orders/show.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detalles del Pedido a Proveedor #') }}{{ $supplierOrder->id }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-3">{{ __('Información del Pedido') }}</h3>
                        <p><strong>Proveedor:</strong> {{ $supplierOrder->supplier->name ?? 'N/A' }}</p>
                        <p><strong>Monto Total:</strong> ${{ number_format($supplierOrder->total_amount, 2) }}</p>
                        <p><strong>Estado:</strong>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{
                                $supplierOrder->status === 'received' ? 'bg-green-100 text-green-800' :
                                ($supplierOrder->status === 'cancelled' ? 'bg-red-100 text-red-800' :
                                ($supplierOrder->status === 'ordered' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800'))
                            }}">
                                {{ ucfirst($supplierOrder->status) }}
                            </span>
                        </p>
                        <p><strong>Fecha del Pedido:</strong> {{ $supplierOrder->order_date ? \Carbon\Carbon::parse($supplierOrder->order_date)->format('d/m/Y') : 'N/A' }}</p>
                        <p><strong>Fecha de Entrega Estimada:</strong> {{ $supplierOrder->expected_delivery_date ? \Carbon\Carbon::parse($supplierOrder->expected_delivery_date)->format('d/m/Y') : 'N/A' }}</p>
                    </div>

                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-3">{{ __('Productos del Pedido') }}</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Producto
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            SKU
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Cantidad
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Costo Unitario
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Subtotal
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse ($supplierOrder->supplierOrderItems as $item)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                {{ $item->product->name ?? 'Producto Eliminado' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $item->product->sku ?? 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $item->quantity }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                ${{ number_format($item->cost_at_order, 2) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                ${{ number_format($item->quantity * $item->cost_at_order, 2) }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                                No hay ítems en este pedido.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <a href="{{ route('supplier-orders.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500 active:bg-gray-700 focus:outline-none focus:border-gray-700 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150 mr-3">
                            {{ __('Volver a Pedidos') }}
                        </a>
                        <a href="{{ route('supplier-orders.edit', $supplierOrder) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                            {{ __('Editar Estado del Pedido') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
