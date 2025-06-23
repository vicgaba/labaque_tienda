{{-- resources/views/products/create.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Crear Nuevo Producto') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Columna 1 --}}
                            <div>
                                <div class="mb-4">
                                    <x-input-label for="name" :value="__('Nombre del Producto')" />
                                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                </div>

                                <div class="mb-4">
                                    <x-input-label for="category_id" :value="__('Categoría')" />
                                    <select id="category_id" name="category_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                        <option value="">Seleccione una categoría</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                                </div>

                                <div class="mb-4">
                                    <x-input-label for="sku" :value="__('SKU (Código de Producto)')" />
                                    <x-text-input id="sku" class="block mt-1 w-full" type="text" name="sku" :value="old('sku')" />
                                    <x-input-error :messages="$errors->get('sku')" class="mt-2" />
                                </div>

                                <div class="mb-4">
                                    <x-input-label for="description" :value="__('Descripción')" />
                                    <textarea id="description" name="description" rows="3" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('description') }}</textarea>
                                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                                </div>
                            </div>

                            {{-- Columna 2 --}}
                            <div>
                                <div class="mb-4">
                                    <x-input-label for="price" :value="__('Precio')" />
                                    <x-text-input id="price" class="block mt-1 w-full" type="number" step="0.01" name="price" :value="old('price')" required />
                                    <x-input-error :messages="$errors->get('price')" class="mt-2" />
                                </div>

                                <div class="mb-4">
                                    <x-input-label for="stock" :value="__('Stock Inicial')" />
                                    <x-text-input id="stock" class="block mt-1 w-full" type="number" name="stock" :value="old('stock')" required />
                                    <x-input-error :messages="$errors->get('stock')" class="mt-2" />
                                </div>

                                <div class="mb-4">
                                    <x-input-label for="size" :value="__('Talla')" />
                                    <select id="size" name="size" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        <option value="">Seleccione una talla</option>
                                        @foreach (['XS', 'S', 'M', 'L', 'XL', 'XXL', 'Único'] as $size)
                                            <option value="{{ $size }}" {{ old('size') == $size ? 'selected' : '' }}>
                                                {{ $size }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('size')" class="mt-2" />
                                </div>

                                <div class="mb-4">
                                    <x-input-label for="color" :value="__('Color')" />
                                    <x-text-input id="color" class="block mt-1 w-full" type="text" name="color" :value="old('color')" />
                                    <x-input-error :messages="$errors->get('color')" class="mt-2" />
                                </div>

                                <div class="mb-4">
                                    <x-input-label for="brand" :value="__('Marca')" />
                                    <x-text-input id="brand" class="block mt-1 w-full" type="text" name="brand" :value="old('brand')" />
                                    <x-input-error :messages="$errors->get('brand')" class="mt-2" />
                                </div>

                                <div class="mb-4">
                                    <x-input-label for="image" :value="__('Imagen del Producto')" />
                                    <input id="image" class="block mt-1 w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none" type="file" name="image">
                                    <x-input-error :messages="$errors->get('image')" class="mt-2" />
                                </div>

                                <div class="mb-4">
                                    <label for="is_active" class="inline-flex items-center">
                                        <input id="is_active" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="is_active" checked>
                                        <span class="ml-2 text-sm text-gray-600">{{ __('Producto Activo') }}</span>
                                    </label>
                                    <x-input-error :messages="$errors->get('is_active')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('products.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500 active:bg-gray-700 focus:outline-none focus:border-gray-700 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150 mr-3">
                                {{ __('Cancelar') }}
                            </a>
                            <x-primary-button>
                                {{ __('Guardar Producto') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
