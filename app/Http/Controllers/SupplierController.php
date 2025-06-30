<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     * Muestra una lista de los recursos (proveedores).
     */
    public function index(Request $request): View
    {
        $search = $request->get('search'); // Obtiene el término de búsqueda de la solicitud
        
        $suppliers = Supplier::latest()
        ->when($search, function ($query) use ($search) {
            return $query->where(function ($q) use ($search) {
                $q->where('phone', 'like', '%' . $search . '%')
//change for cuit                  ->orWhere('dni', 'like', '%' . $search . '%')
                  ->orWhere('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        })->paginate(10); // Obtiene todos los proveedores, paginados

        return view('suppliers.index', compact('suppliers')); // Pasa los proveedores a la vista
    }

    /**
     * Show the form for creating a new resource.
     * Muestra el formulario para crear un nuevo recurso (proveedor).
     */
    public function create(): View
    {
        return view('suppliers.create');
    }

    /**
     * Store a newly created resource in storage.
     * Almacena un nuevo recurso (proveedor) en la base de datos.
     */
    public function store(Request $request): RedirectResponse
    {
        // Valida los datos del formulario
        $request->validate([
            'name' => 'required|string|max:255|unique:suppliers,name',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255|unique:suppliers,email',
            'address' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        // Crea un nuevo proveedor
        Supplier::create($request->all());

        // Redirige de vuelta al índice de proveedores con un mensaje de éxito
        return redirect()->route('suppliers.index')->with('success', 'Proveedor creado exitosamente.');
    }

    /**
     * Display the specified resource.
     * Muestra el recurso (proveedor) especificado.
     * En este caso, redirigiremos a la vista de edición.
     */
    public function show(Supplier $supplier): RedirectResponse
    {
        return redirect()->route('suppliers.edit', $supplier);
    }

    /**
     * Show the form for editing the specified resource.
     * Muestra el formulario para editar el recurso (proveedor) especificado.
     */
    public function edit(Supplier $supplier): View
    {
        return view('suppliers.edit', compact('supplier'));
    }

    /**
     * Update the specified resource in storage.
     * Actualiza el recurso (proveedor) especificado en la base de datos.
     */
    public function update(Request $request, Supplier $supplier): RedirectResponse
    {
        // Valida los datos del formulario
        $request->validate([
            'name' => 'required|string|max:255|unique:suppliers,name,' . $supplier->id,
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255|unique:suppliers,email,' . $supplier->id,
            'address' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        // Actualiza el proveedor
        $supplier->update($request->all());

        // Redirige de vuelta al índice de proveedores con un mensaje de éxito
        return redirect()->route('suppliers.index')->with('success', 'Proveedor actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     * Elimina el recurso (proveedor) especificado de la base de datos.
     */
    public function destroy(Supplier $supplier): RedirectResponse
    {
        try {
            $supplier->delete();
            return redirect()->route('suppliers.index')->with('success', 'Proveedor eliminado exitosamente.');
        } catch (\Illuminate\Database\QueryException $e) {
            // Esto es importante si el proveedor tiene pedidos asociados
            return redirect()->route('suppliers.index')->with('error', 'No se puede eliminar el proveedor porque tiene pedidos de compra asociados.');
        }
    }
}
