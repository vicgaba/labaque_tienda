<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\CustomerOrderController;
use App\Http\Controllers\SupplierOrderController; 
use App\Models\Supplier;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';

// Rutas accesibles solo por administradores
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', function () {
        return "Bienvenido, Administrador!";
    })->name('admin.dashboard');
    Route::resource('categories', CategoryController::class);
    Route::resource('products', ProductController::class);
    Route::resource('suppliers', SupplierController::class);
    Route::resource('clients', ClientController::class);
    Route::resource('supplier-orders', SupplierOrderController::class);
    


    // Ejemplo: Rutas para gestionar usuarios (solo admin)
    Route::get('/admin/users', function () {
        return "Gestión de Usuarios (Solo Admin)";
    });
    // Aquí irían las rutas para CRUD de productos, categorías, proveedores, etc.
    // ...
});

// Rutas accesibles por administradores y vendedores
Route::middleware(['auth', 'role:admin,seller'])->group(function () {
    Route::get('/users/dashboard', function () {
        return view('dashboard'); // El dashboard principal para admin y vendedor
    })->name('dashboard');
    Route::resource('customer-orders', CustomerOrderController::class);

    // Ejemplo: Rutas para gestión de ventas y stock (admin y vendedor)
    Route::get('/sales', function () {
        return "Gestión de Ventas";
    });
    Route::get('/products/view', function () {
        return "Ver productos y stock";
    });
    // ...
});

// Rutas accesibles solo por clientes (una vez que estén logueados)
Route::middleware(['auth', 'role:client'])->group(function () {
    Route::get('/client/dashboard', function () {
        return "Bienvenido, Cliente!";
    })->name('client.dashboard');

    // Ejemplo: Ver historial de pedidos del cliente
    Route::get('/client/orders', function () {
        return "Historial de tus pedidos";
    });
});