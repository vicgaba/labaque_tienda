<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            {{-- Contenido de la navegación que antes estaba en navigation.blade.php --}}
            <nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
                <!-- Primary Navigation Menu -->
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="shrink-0 flex items-center">
                                <a href="{{ route('dashboard') }}">
                                    <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                                </a>
                            </div>

                            <!-- Navigation Links -->
                            <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                                <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                                    {{ __('Dashboard') }}
                                </x-nav-link>

                                {{-- Enlaces para Administrador --}}
                                @if (Auth::user()->isAdmin())
                                    <x-nav-link :href="route('categories.index')" :active="request()->routeIs('categories.*')">
                                        {{ __('Categorías') }}
                                    </x-nav-link>
                                    <x-nav-link :href="route('products.index')" :active="request()->routeIs('products.*')">
                                        {{ __('Productos') }}
                                    </x-nav-link>
                                    <x-nav-link :href="route('suppliers.index')" :active="request()->routeIs('suppliers.*')">
                                        {{ __('Proveedores') }}
                                    </x-nav-link>
                                    <x-nav-link :href="route('clients.index')" :active="request()->routeIs('clients.*')">
                                        {{ __('Clientes') }}
                                    </x-nav-link>
                                    <x-nav-link :href="route('supplier-orders.index')" :active="request()->routeIs('supplier-orders.*')">
                                        {{ __('Compras a Proveedores') }}
                                    </x-nav-link>
                                @endif

                                {{-- Enlaces para Administrador y Vendedor --}}
                                @if (Auth::user()->isAdmin() || Auth::user()->isSeller())
                                    <x-nav-link :href="route('customer-orders.index')" :active="request()->routeIs('customer-orders.*')">
                                        {{ __('Ventas a Clientes') }}
                                    </x-nav-link>
                                @endif

                                {{-- Enlaces para Cliente (puedes añadir más si se implementan vistas de cliente) --}}
                                @if (Auth::user()->isClient())
                                    <x-nav-link :href="route('client.dashboard')" :active="request()->routeIs('client.dashboard')">
                                        {{ __('Mi Dashboard') }}
                                    </x-nav-link>
                                    <x-nav-link :href="route('client.orders')" :active="request()->routeIs('client.orders')">
                                        {{ __('Mis Pedidos') }}
                                    </x-nav-link>
                                @endif
                            </div>
                        </div>

                        <div class="hidden sm:flex sm:items-center sm:ml-6">
                            <!-- Settings Dropdown -->
                            <x-dropdown align="right" width="48">
                                <x-slot name="trigger">
                                    <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                        <div>{{ Auth::user()->name }}</div>

                                        <div class="ml-1">
                                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </button>
                                </x-slot>

                                <x-slot name="content">
                                    <x-dropdown-link :href="route('profile.edit')">
                                        {{ __('Perfil') }}
                                    </x-dropdown-link>

                                    <!-- Authentication -->
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf

                                        <x-dropdown-link :href="route('logout')"
                                                onclick="event.preventDefault();
                                                            this.closest('form').submit();">
                                            {{ __('Cerrar Sesión') }}
                                        </x-dropdown-link>
                                    </form>
                                </x-slot>
                            </x-dropdown>
                        </div>

                        <!-- Hamburger -->
                        <div class="-mr-2 flex items-center sm:hidden">
                            <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                    <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
                    <div class="pt-2 pb-3 space-y-1">
                        <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                            {{ __('Dashboard') }}
                        </x-responsive-nav-link>

                        {{-- Enlaces para Administrador (Responsive) --}}
                        @if (Auth::user()->isAdmin())
                            <x-responsive-nav-link :href="route('categories.index')" :active="request()->routeIs('categories.*')">
                                {{ __('Categorías') }}
                            </x-responsive-nav-link>
                            <x-responsive-nav-link :href="route('products.index')" :active="request()->routeIs('products.*')">
                                {{ __('Productos') }}
                            </x-responsive-nav-link>
                            <x-responsive-nav-link :href="route('suppliers.index')" :active="request()->routeIs('suppliers.*')">
                                {{ __('Proveedores') }}
                            </x-responsive-nav-link>
                            <x-responsive-nav-link :href="route('clients.index')" :active="request()->routeIs('clients.*')">
                                {{ __('Clientes') }}
                            </x-responsive-nav-link>
                            <x-responsive-nav-link :href="route('supplier-orders.index')" :active="request()->routeIs('supplier-orders.*')">
                                {{ __('Compras a Proveedores') }}
                            </x-responsive-nav-link>
                        @endif

                        {{-- Enlaces para Administrador y Vendedor (Responsive) --}}
                        @if (Auth::user()->isAdmin() || Auth::user()->isSeller())
                            <x-responsive-nav-link :href="route('customer-orders.index')" :active="request()->routeIs('customer-orders.*')">
                                {{ __('Ventas a Clientes') }}
                            </x-responsive-nav-link>
                        @endif

                        {{-- Enlaces para Cliente (Responsive) --}}
                        @if (Auth::user()->isClient())
                            <x-responsive-nav-link :href="route('client.dashboard')" :active="request()->routeIs('client.dashboard')">
                                {{ __('Mi Dashboard') }}
                            </x-responsive-nav-link>
                            <x-responsive-nav-link :href="route('client.orders')" :active="request()->routeIs('client.orders')">
                                {{ __('Mis Pedidos') }}
                            </x-responsive-nav-link>
                        @endif
                    </div>

                    <!-- Responsive Settings Options -->
                    <div class="pt-4 pb-1 border-t border-gray-200">
                        <div class="px-4">
                            <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                            <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <x-responsive-nav-link :href="route('profile.edit')">
                                {{ __('Perfil') }}
                            </x-responsive-nav-link>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf

                                <x-responsive-nav-link :href="route('logout')"
                                        onclick="event.preventDefault();
                                                    this.closest('form').submit();">
                                    {{ __('Cerrar Sesión') }}
                                </x-responsive-nav-link>
                            </form>
                        </div>
                    </div>
                </div>
            </nav>
            {{-- Fin del contenido de la navegación --}}

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
