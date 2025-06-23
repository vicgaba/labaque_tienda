<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\View\View; // Importar la clase View para type-hinting
use Illuminate\Http\RedirectResponse; // Importar RedirectResponse para type-hinting

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     * Muestra una lista de los recursos (categorías).
     */
    public function index(): View
    {
        $categories = Category::all(); // Obtiene todas las categorías
        return view('categories.index', compact('categories')); // Pasa las categorías a la vista
    }

    /**
     * Show the form for creating a new resource.
     * Muestra el formulario para crear un nuevo recurso (categoría).
     */
    public function create(): View
    {
        return view('categories.create');
    }

    /**
     * Store a newly created resource in storage.
     * Almacena un nuevo recurso (categoría) en la base de datos.
     */
    public function store(Request $request): RedirectResponse
    {
        // Valida los datos del formulario
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string',
        ]);

        // Crea una nueva categoría
        Category::create($request->all());

        // Redirige de vuelta al índice de categorías con un mensaje de éxito
        return redirect()->route('categories.index')->with('success', 'Categoría creada exitosamente.');
    }

    /**
     * Display the specified resource.
     * Muestra el recurso (categoría) especificado.
     * En este caso, no necesitamos una vista separada para 'show'
     * ya que la información se ve en 'index' o 'edit'.
     */
    public function show(Category $category): RedirectResponse
    {
        // Opcional: Podrías redirigir a la página de edición o a una vista de detalles
        return redirect()->route('categories.edit', $category);
    }

    /**
     * Show the form for editing the specified resource.
     * Muestra el formulario para editar el recurso (categoría) especificado.
     */
    public function edit(Category $category): View
    {
        return view('categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     * Actualiza el recurso (categoría) especificado en la base de datos.
     */
    public function update(Request $request, Category $category): RedirectResponse
    {
        // Valida los datos del formulario
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id, // unique:tabla,columna,id_a_ignorar
            'description' => 'nullable|string',
        ]);

        // Actualiza la categoría
        $category->update($request->all());

        // Redirige de vuelta al índice de categorías con un mensaje de éxito
        return redirect()->route('categories.index')->with('success', 'Categoría actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     * Elimina el recurso (categoría) especificado de la base de datos.
     */
    public function destroy(Category $category): RedirectResponse
    {
        // Intenta eliminar la categoría
        try {
            $category->delete();
            return redirect()->route('categories.index')->with('success', 'Categoría eliminada exitosamente.');
        } catch (\Illuminate\Database\QueryException $e) {
            // Captura la excepción si hay claves foráneas (productos asociados)
            return redirect()->route('categories.index')->with('error', 'No se puede eliminar la categoría porque tiene productos asociados.');
        }
    }
}