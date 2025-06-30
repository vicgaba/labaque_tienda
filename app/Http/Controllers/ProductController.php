<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource with filters and search.
     */
    public function index(Request $request): View
    {
        $query = Product::with('category');

        // Búsqueda por nombre o SKU
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%");
            });
        }

        // Filtro por categoría
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filtro por estado activo/inactivo
        if ($request->filled('status')) {
            $query->where('active', $request->status === 'active');
        }

        // Filtro por stock bajo (menos de 10 unidades)
        if ($request->filled('low_stock')) {
            $query->where('stock', '<', 10);
        }

        // Ordenamiento
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $products = $query->paginate(15)->withQueryString();
        $categories = Category::active()->get(); // Solo categorías activas

        return view('products.index', compact('products', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $categories = Category::active()->get(); // Solo categorías activas
        return view('products.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateProduct($request);

        DB::beginTransaction();
        try {
            // Generar SKU automático si no se proporciona
            if (empty($validated['sku'])) {
                $validated['sku'] = $this->generateSKU($validated['name']);
            }

            // Manejar imagen
            $validated['image_path'] = $this->handleImageUpload($request);

            // Manejar checkbox active
            $validated['active'] = $request->has('active');

            Product::create($validated);

            DB::commit();
            return redirect()->route('products.index')
                ->with('success', 'Producto creado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            // Eliminar imagen si se subió pero falló la creación
            if (isset($validated['image_path'])) {
                Storage::disk('public')->delete($validated['image_path']);
            }
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al crear el producto: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product): View
    {
        return view('products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product): View
    {
        $categories = Category::active()->get();
        return view('products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $this->validateProduct($request, $product->id);

        DB::beginTransaction();
        try {
            // Manejar imagen
            if ($request->hasFile('image')) {
                // Eliminar imagen anterior
                if ($product->image_path) {
                    Storage::disk('public')->delete($product->image_path);
                }
                $validated['image_path'] = $this->handleImageUpload($request);
            }

            // Manejar checkbox active
            $validated['active'] = $request->has('active');

            $product->update($validated);

            DB::commit();
            return redirect()->route('products.index')
                ->with('success', 'Producto actualizado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al actualizar el producto: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product): RedirectResponse
    {
        DB::beginTransaction();
        try {
            // Eliminar imagen asociada
            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }

            $product->delete();

            DB::commit();
            return redirect()->route('products.index')
                ->with('success', 'Producto eliminado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('products.index')
                ->with('error', 'No se puede eliminar el producto porque tiene registros asociados.');
        }
    }

    /**
     * Toggle the active status of the product.
     */
    public function toggle(Product $product): RedirectResponse
    {
        $product->update(['active' => !$product->active]);
        
        $status = $product->active ? 'activado' : 'desactivado';
        return redirect()->route('products.index')
            ->with('success', "Producto {$status} exitosamente.");
    }

    /**
     * Bulk actions for multiple products.
     */
    public function bulkAction(Request $request): RedirectResponse
    {
        $request->validate([
            'products' => 'required|array',
            'products.*' => 'exists:products,id',
            'action' => 'required|in:activate,deactivate,delete'
        ]);

        $products = Product::whereIn('id', $request->products);
        $count = $products->count();

        DB::beginTransaction();
        try {
            switch ($request->action) {
                case 'activate':
                    $products->update(['active' => true]);
                    $message = "{$count} productos activados exitosamente.";
                    break;
                case 'deactivate':
                    $products->update(['active' => false]);
                    $message = "{$count} productos desactivados exitosamente.";
                    break;
                case 'delete':
                    // Eliminar imágenes asociadas
                    $productsToDelete = $products->get();
                    foreach ($productsToDelete as $product) {
                        if ($product->image_path) {
                            Storage::disk('public')->delete($product->image_path);
                        }
                    }
                    $products->delete();
                    $message = "{$count} productos eliminados exitosamente.";
                    break;
            }

            DB::commit();
            return redirect()->route('products.index')->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('products.index')
                ->with('error', 'Error al realizar la acción: ' . $e->getMessage());
        }
    }

    /**
     * Export products to CSV.
     */
    public function export(): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $products = Product::with('category')->get();
        
        $filename = 'productos_' . now()->format('Y-m-d_H-i-s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($products) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Nombre', 'SKU', 'Categoría', 'Precio', 'Stock', 'Estado', 'Marca', 'Talla', 'Color']);
            
            foreach ($products as $product) {
                fputcsv($file, [
                    $product->id,
                    $product->name,
                    $product->sku,
                    $product->category->name ?? 'Sin categoría',
                    $product->price,
                    $product->stock,
                    $product->active ? 'Activo' : 'Inactivo',
                    $product->brand,
                    $product->size,
                    $product->color,
                ]);
            }
            fclose($file);
        };

        return response()->streamDownload($callback, $filename, $headers);
    }

    /**
     * Validate product data.
     */
    private function validateProduct(Request $request, ?int $productId = null): array
    {
        $skuRule = $productId ? 
            "nullable|string|max:255|unique:products,sku,{$productId}" : 
            'nullable|string|max:255|unique:products,sku';

        return $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'sku' => $skuRule,
            'description' => 'nullable|string|max:1000',
            'price' => 'required|numeric|min:0|max:999999.99',
            'stock' => 'required|integer|min:0|max:999999',
            'size' => 'nullable|in:XS,S,M,L,XL,XXL,Único',
            'color' => 'nullable|string|max:100',
            'brand' => 'nullable|string|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'active' => 'sometimes|boolean',
        ]);
    }

    /**
     * Handle image upload.
     */
    private function handleImageUpload(Request $request): ?string
    {
        if (!$request->hasFile('image')) {
            return null;
        }

        $image = $request->file('image');
        $filename = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
        
        return $image->storeAs('products', $filename, 'public');
    }

    /**
     * Generate unique SKU.
     */
    private function generateSKU(string $productName): string
    {
        $base = strtoupper(Str::slug(Str::limit($productName, 10, ''), ''));
        $timestamp = now()->format('ymd');
        $random = Str::random(3);
        
        $sku = $base . '-' . $timestamp . '-' . $random;
        
        // Asegurar que sea único
        $counter = 1;
        $originalSku = $sku;
        while (Product::where('sku', $sku)->exists()) {
            $sku = $originalSku . '-' . $counter;
            $counter++;
        }
        
        return $sku;
    }
}