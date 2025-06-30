{{-- resources/views/categories/edit.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Categoría') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium text-gray-900">Editar Categoría: {{ $category->name }}</h3>
                        <a href="{{ route('categories.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 active:bg-gray-500 focus:outline-none focus:border-gray-500 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                            {{ __('Volver al Listado') }}
                        </a>
                    </div>

                    {{-- Mostrar errores de validación --}}
                    @if ($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <strong class="font-bold">¡Ups! Algo salió mal.</strong>
                            <ul class="mt-2 list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('categories.update', $category) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        {{-- Campo Nombre --}}
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">
                                {{ __('Nombre de la Categoría') }} <span class="text-red-500">*</span>
                            </label>
                            <input 
                                id="name" 
                                name="name" 
                                type="text" 
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('name') border-red-300 @enderror" 
                                value="{{ old('name', $category->name) }}" 
                                required 
                                autofocus
                                placeholder="Ingresa el nombre de la categoría"
                            >
                            @error('name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Campo Descripción --}}
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700">
                                {{ __('Descripción') }}
                            </label>
                            <textarea 
                                id="description" 
                                name="description" 
                                rows="4" 
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('description') border-red-300 @enderror"
                                placeholder="Ingresa una descripción opcional para la categoría"
                            >{{ old('description', $category->description) }}</textarea>
                            @error('description')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Campo Estado Activo --}}
                        <div>
                            <div class="flex items-center">
                                <input 
                                    id="active" 
                                    name="active" 
                                    type="checkbox" 
                                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded" 
                                    value="1"
                                    {{ old('active', $category->active) ? 'checked' : '' }}
                                >
                                <label for="active" class="ml-2 block text-sm text-gray-900">
                                    {{ __('Categoría activa') }}
                                </label>
                            </div>
                            <p class="mt-1 text-sm text-gray-500">
                                Las categorías inactivas no aparecerán en las listas de selección.
                            </p>
                            @error('active')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Información adicional --}}
                        <div class="bg-gray-50 p-4 rounded-md">
                            <h4 class="text-sm font-medium text-gray-900 mb-2">Información de la Categoría</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600">
                                <div>
                                    <span class="font-medium">ID:</span> {{ $category->id }}
                                </div>
                                <div>
                                    <span class="font-medium">Estado actual:</span> 
                                    @if($category->active)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Activa
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Inactiva
                                        </span>
                                    @endif
                                </div>
                                <div>
                                    <span class="font-medium">Creada:</span> {{ $category->created_at->format('d/m/Y H:i') }}
                                </div>
                                <div>
                                    <span class="font-medium">Última actualización:</span> {{ $category->updated_at->format('d/m/Y H:i') }}
                                </div>
                            </div>
                        </div>

                        {{-- Botones de acción --}}
                        <div class="flex items-center justify-end space-x-4">
                            <a href="{{ route('categories.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                                {{ __('Cancelar') }}
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-green uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                                {{ __('Actualizar Categoría') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>