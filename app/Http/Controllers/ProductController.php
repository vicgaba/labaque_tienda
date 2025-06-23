<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category; // Importar el modelo Category para el dropdown
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage; // Para manejar archivos (imágenes)

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     * Muestra una lista de los recursos (productos).
     */
    public function index(): View
    {
        $products = Product::with('category')->latest()->paginate(10); // Obtiene productos con su categoría, paginados
        return view('products.index', compact('products')); // Pasa los productos a la vista
    }

    /**
     * Show the form for creating a new resource.
     * Muestra el formulario para crear un nuevo recurso (producto).
     */
    public function create(): View
    {
        $categories = Category::all(); // Obtiene todas las categorías para el dropdown
        return view('products.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     * Almacena un nuevo recurso (producto) en la base de datos.
     */
    public function store(Request $request): RedirectResponse
    {
        // Valida los datos del formulario
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id', // Debe existir en la tabla categories
            'sku' => 'nullable|string|max:255|unique:products,sku',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'size' => 'nullable|in:XS,S,M,L,XL,XXL,Único',
            'color' => 'nullable|string|max:255',
            'brand' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Validación para la imagen
            'is_active' => 'boolean',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            // Guarda la imagen en storage/app/public/products
            $imagePath = $request->file('image')->store('products', 'public');
        }

        // Crea un nuevo producto
        Product::create(array_merge($validated, ['image_path' => $imagePath]));

        // Redirige de vuelta al índice de productos con un mensaje de éxito
        return redirect()->route('products.index')->with('success', 'Producto creado exitosamente.');
    }

    /**
     * Display the specified resource.
     * Muestra el recurso (producto) especificado.
     * En este caso, redirigiremos a la vista de edición.
     */
    public function show(Product $product): RedirectResponse
    {
        return redirect()->route('products.edit', $product);
    }

    /**
     * Show the form for editing the specified resource.
     * Muestra el formulario para editar el recurso (producto) especificado.
     */
    public function edit(Product $product): View
    {
        $categories = Category::all(); // Obtiene todas las categorías para el dropdown
        return view('products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     * Actualiza el recurso (producto) especificado en la base de datos.
     */
    public function update(Request $request, Product $product): RedirectResponse
    {
        // Valida los datos del formulario
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'sku' => 'nullable|string|max:255|unique:products,sku,' . $product->id,
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0', // Permite actualizar stock directamente por ahora
            'size' => 'nullable|in:XS,S,M,L,XL,XXL,Único',
            'color' => 'nullable|string|max:255',
            'brand' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'is_active' => 'boolean',
        ]);

        // Manejo de la imagen: si se sube una nueva, eliminar la anterior
        if ($request->hasFile('image')) {
            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path); // Elimina la imagen anterior
            }
            $imagePath = $request->file('image')->store('products', 'public');
            $validated['image_path'] = $imagePath;
        }

        // Actualiza el producto
        $product->update($validated);

        // Redirige de vuelta al índice de productos con un mensaje de éxito
        return redirect()->route('products.index')->with('success', 'Producto actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     * Elimina el recurso (producto) especificado de la base de datos.
     */
    public function destroy(Product $product): RedirectResponse
    {
        // Eliminar la imagen asociada si existe
        if ($product->image_path) {
            Storage::disk('public')->delete($product->image_path);
        }

        try {
            $product->delete();
            return redirect()->route('products.index')->with('success', 'Producto eliminado exitosamente.');
        } catch (\Illuminate\Database\QueryException $e) {
            // Esto es importante si el producto tiene movimientos de stock o está en pedidos históricos
            return redirect()->route('products.index')->with('error', 'No se puede eliminar el producto porque tiene registros asociados (ej. movimientos de stock o ítems de pedidos).');
        }
    }
}
