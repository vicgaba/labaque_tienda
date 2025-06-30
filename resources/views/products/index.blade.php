<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Productos') }}
            </h2>
            <div class="flex space-x-2">
<!--                <a href="{{ route('products.export') }}" 
                   class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    Exportar CSV
                </a>
-->
                <a href="{{ route('products.create') }}" 
                   class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Nuevo Producto
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <!-- Filtros -->
                <div class="p-6 bg-gray-50 border-b border-gray-200">
                    <form method="GET" action="{{ route('products.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                        <!-- Búsqueda -->
                        <div>
                            <input type="text" 
                                   name="search" 
                                   value="{{ request('search') }}"
                                   placeholder="Buscar por nombre, SKU o marca..."
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <!-- Categoría -->
                        <div>
                            <select name="category_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Todas las categorías</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Estado -->
                        <div>
                            <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Todos los estados</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Activos</option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactivos</option>
                            </select>
                        </div>

                        <!-- Stock bajo -->
                        <div class="flex items-center">
                            <input type="checkbox" 
                                   name="low_stock" 
                                   value="1" 
                                   {{ request('low_stock') ? 'checked' : '' }}
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label class="ml-2 block text-sm text-gray-700">Stock bajo</label>
                        </div>

                        <!-- Botones -->
                        <div class="flex space-x-2">
                            <button type="submit" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Filtrar
                            </button>
                            <a href="{{ route('products.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Limpiar
                            </a>
                        </div>
                    </form>
                </div>

                <!-- Acciones masivas -->
                <div class="p-4 bg-yellow-50 border-b border-gray-200">
                    <form id="bulk-form" method="POST" action="{{ route('products.bulk-action') }}">
                        @csrf
                        <div class="flex items-center space-x-4">
                            <select name="action" class="px-3 py-2 border border-gray-300 rounded-md">
                                <option value="">Seleccionar acción</option>
                                <option value="activate">Activar seleccionados</option>
                                <option value="deactivate">Desactivar seleccionados</option>
                                <option value="delete">Eliminar seleccionados</option>
                            </select>
                            <button type="submit" class="bg-orange-500 hover:bg-orange-700 text-white font-bold py-2 px-4 rounded">
                                Aplicar
                            </button>
                            <span class="text-sm text-gray-600">
                                <span id="selected-count">0</span> productos seleccionados
                            </span>
                        </div>
                    </form>
                </div>

                <!-- Tabla de productos -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <input type="checkbox" id="select-all" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Imagen
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'name', 'sort_order' => request('sort_order') === 'asc' ? 'desc' : 'asc']) }}" class="hover:text-gray-700">
                                        Producto
                                        @if(request('sort_by') === 'name')
                                            <span class="ml-1">{{ request('sort_order') === 'asc' ? '↑' : '↓' }}</span>
                                        @endif
                                    </a>
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    SKU
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Categoría
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'price', 'sort_order' => request('sort_order') === 'asc' ? 'desc' : 'asc']) }}" class="hover:text-gray-700">
                                        Precio
                                        @if(request('sort_by') === 'price')
                                            <span class="ml-1">{{ request('sort_order') === 'asc' ? '↑' : '↓' }}</span>
                                        @endif
                                    </a>
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'stock', 'sort_order' => request('sort_order') === 'asc' ? 'desc' : 'asc']) }}" class="hover:text-gray-700">
                                        Stock
                                        @if(request('sort_by') === 'stock')
                                            <span class="ml-1">{{ request('sort_order') === 'asc' ? '↑' : '↓' }}</span>
                                        @endif
                                    </a>
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Estado
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($products as $product)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input type="checkbox" name="products[]" value="{{ $product->id }}" class="product-checkbox h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($product->image_path)
                                            <img src="{{ Storage::url($product->image_path) }}" 
                                                 alt="{{ $product->name }}" 
                                                 class="h-16 w-16 object-cover rounded-lg">
                                        @else
                                            <div class="h-16 w-16 bg-gray-200 rounded-lg flex items-center justify-center">
                                                <span class="text-gray-400 text-xs">Sin imagen</span>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                                        @if($product->brand)
                                            <div class="text-sm text-gray-500">{{ $product->brand }}</div>
                                        @endif
                                        @if($product->size || $product->color)
                                            <div class="text-xs text-gray-400">
                                                @if($product->size) Talla: {{ $product->size }} @endif
                                                @if($product->color) Color: {{ $product->color }} @endif
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $product->sku }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $product->category->name ?? 'Sin categoría' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        ${{ number_format($product->price, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <span class="@if($product->stock < 10) text-red-600 font-semibold @endif">
                                            {{ $product->stock }}
                                        </span>
                                        @if($product->stock < 10)
                                            <span class="text-xs text-red-500 block">Stock bajo</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <form method="POST" action="{{ route('products.toggle', $product) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $product->active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $product->active ? 'Activo' : 'Inactivo' }}
                                            </button>
                                        </form>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('products.show', $product) }}" class="text-blue-600 hover:text-blue-900">Ver</a>
                                            <a href="{{ route('products.edit', $product) }}" class="text-indigo-600 hover:text-indigo-900">Editar</a>
                                            <form method="POST" action="{{ route('products.destroy', $product) }}" class="inline" onsubmit="return confirm('¿Estás seguro de que quieres eliminar este producto?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">Eliminar</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                        No se encontraron productos.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $products->links() }}
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Seleccionar todos los checkboxes
        document.getElementById('select-all').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.product-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateSelectedCount();
        });

        // Actualizar contador de seleccionados
        document.querySelectorAll('.product-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', updateSelectedCount);
        });

        function updateSelectedCount() {
            const selected = document.querySelectorAll('.product-checkbox:checked').length;
            document.getElementById('selected-count').textContent = selected;
            
            // Agregar los IDs seleccionados al formulario
            const form = document.getElementById('bulk-form');
            // Remover inputs anteriores
            form.querySelectorAll('input[name="products[]"]').forEach(input => input.remove());
            
            // Agregar nuevos inputs
            document.querySelectorAll('.product-checkbox:checked').forEach(checkbox => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'products[]';
                input.value = checkbox.value;
                form.appendChild(input);
            });
        }

        // Confirmar acciones masivas
        document.getElementById('bulk-form').addEventListener('submit', function(e) {
            const action = this.querySelector('select[name="action"]').value;
            const selected = document.querySelectorAll('.product-checkbox:checked').length;
            
            if (!action) {
                e.preventDefault();
                alert('Por favor selecciona una acción.');
                return;
            }
            
            if (selected === 0) {
                e.preventDefault();
                alert('Por favor selecciona al menos un producto.');
                return;
            }
            
            let message = '';
            switch(action) {
                case 'delete':
                    message = `¿Estás seguro de que quieres eliminar ${selected} productos?`;
                    break;
                case 'activate':
                    message = `¿Estás seguro de que quieres activar ${selected} productos?`;
                    break;
                case 'deactivate':
                    message = `¿Estás seguro de que quieres desactivar ${selected} productos?`;
                    break;
            }
            
            if (!confirm(message)) {
                e.preventDefault();
            }
        });
    </script>
    @endpush
</x-app-layout>