<?php

namespace App\Http\Controllers;

use App\Models\CustomerOrder;
use App\Models\Client;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
// Importa las clases para exportación si las usas (ej. para PDF o Excel)
// use Barryvdh\DomPDF\Facade\Pdf; 
// use App\Exports\CustomerOrdersExport;
// use Maatwebsite\Excel\Facades\Excel;

class CustomerOrderController extends Controller
{
    /**
     * Muestra una lista de las órdenes con filtros y ordenamiento.
     */
    public function index(Request $request): View
    {
        // Inicia la consulta base con las relaciones necesarias para evitar N+1
        $query = CustomerOrder::with(['client.user', 'customerOrderItems.product']);

        // 1. Filtro de búsqueda general
        $query->when($request->filled('search'), function ($q) use ($request) {
            $search = $request->search;
            // Agrupa las condiciones de búsqueda para que no interfieran con otros filtros
            $q->where(function($subQuery) use ($search) {
                $subQuery->where('id', 'like', "%{$search}%") // Buscar por ID de orden
                         ->orWhereHas('client.user', function ($userQuery) use ($search) {
                             $userQuery->where('name', 'like', "%{$search}%")
                                       ->orWhere('email', 'like', "%{$search}%");
                         })
                         ->orWhereHas('customerOrderItems.product', function ($productQuery) use ($search) {
                             $productQuery->where('name', 'like', "%{$search}%");
                         });
            });
        });

        // 2. Filtros específicos
        $query->when($request->filled('status'), function ($q) use ($request) {
            $q->where('status', $request->status);
        });

        $query->when($request->filled('date_from'), function ($q) use ($request) {
            $q->whereDate('created_at', '>=', $request->date_from);
        });

        $query->when($request->filled('date_to'), function ($q) use ($request) {
            $q->whereDate('created_at', '<=', $request->date_to);
        });

        $query->when($request->filled('min_amount'), function ($q) use ($request) {
            $q->where('total_amount', '>=', $request->min_amount);
        });

        $query->when($request->filled('max_amount'), function ($q) use ($request) {
            $q->where('total_amount', '<=', $request->max_amount);
        });
        
        // 3. Lógica de Ordenamiento
        $sortBy = $request->input('sort_by', 'created_at_desc'); // Por defecto, más recientes
        
        switch ($sortBy) {
            case 'created_at_asc':
                $query->orderBy('created_at', 'asc');
                break;
            case 'total_amount_desc':
                $query->orderBy('total_amount', 'desc');
                break;
            case 'total_amount_asc':
                $query->orderBy('total_amount', 'asc');
                break;
            case 'client_name':
                // Se necesita un join para ordenar por un campo de una tabla relacionada
                $query->join('clients', 'customer_orders.client_id', '=', 'clients.id')
                      ->join('users', 'clients.user_id', '=', 'users.id')
                      ->orderBy('users.name', 'asc')
                      ->select('customer_orders.*'); // Evita la ambigüedad de columnas
                break;
            case 'id_asc':
                $query->orderBy('id', 'asc');
                break;
            case 'id_desc':
                $query->orderBy('id', 'desc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        // 4. Paginación
        // Se usa `appends` para que los parámetros de filtro se mantengan en los enlaces de paginación
        $customerOrders = $query->paginate(10)->appends($request->query());
        
        return view('customer_orders.index', compact('customerOrders'));
    }

    /**
     * Muestra el formulario para crear una nueva orden.
     */
    public function create(): View
    {
        $clients = Client::with('user')->get()->sortBy('user.name');
        $products = Product::where('active', true)->where('stock', '>', 0)->orderBy('name')->get();
        return view('customer_orders.create', compact('clients', 'products'));
    }

    /**
     * Almacena una nueva orden en la base de datos.
     */
    public function store(Request $request): RedirectResponse
    {
        Log::info('CustomerOrder Store - Request data:', $request->all());

        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'product_ids' => 'required|array|min:1',
            'product_ids.*' => 'required|exists:products,id',
            'quantities' => 'required|array|min:1',
            'quantities.*' => 'required|integer|min:1',
            'shipping_address' => 'nullable|string|max:500',
            'status' => 'required|in:pending,processing,completed,cancelled',
        ]);

        if (count($validated['product_ids']) !== count($validated['quantities'])) {
            return redirect()->back()->withInput()->withErrors(['quantities' => 'La cantidad de productos y cantidades no coincide.']);
        }

        DB::beginTransaction();
        try {
            $totalAmount = 0;
            $orderItemsData = [];

            foreach ($validated['product_ids'] as $index => $productId) {
                $product = Product::find($productId);
                $quantity = (int) $validated['quantities'][$index];

                if (!$product) {
                    throw new \Exception("Producto con ID {$productId} no encontrado.");
                }
                if ($product->stock < $quantity) {
                    throw new \Exception("Stock insuficiente para {$product->name}. Stock disponible: {$product->stock}");
                }

                $orderItemsData[] = [
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'price_at_order' => $product->price,
                ];
                $totalAmount += ($quantity * $product->price);
            }

            $customerOrder = CustomerOrder::create([
                'client_id' => $validated['client_id'],
                'total_amount' => $totalAmount,
                'status' => $validated['status'],
                'shipping_address' => $validated['shipping_address'],
            ]);

            Log::info('CustomerOrder created:', ['id' => $customerOrder->id]);

            foreach ($orderItemsData as $itemData) {
                $customerOrder->customerOrderItems()->create($itemData);
                $product = Product::find($itemData['product_id']);
                if ($product) {
                    $product->decrement('stock', $itemData['quantity']);
                    
                    // Asegúrate que tu modelo CustomerOrder tiene la relación stockMovements
                    // if (method_exists($customerOrder, 'stockMovements')) {
                    //     StockMovement::create([
                    //         'product_id' => $itemData['product_id'],
                    //         'user_id' => Auth::id(),
                    //         'type' => 'out',
                    //         'quantity' => $itemData['quantity'],
                    //         'reason' => 'Venta a cliente',
                    //         'movable_id' => $customerOrder->id,
                    //         'movable_type' => CustomerOrder::class,
                    //     ]);
                    // }
                }
            }
            DB::commit();
            return redirect()->route('customer-orders.index')->with('success', 'Venta registrada exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('CustomerOrder store error:', ['error' => $e->getMessage()]);
            return redirect()->back()->withInput()->with('error', 'Error al registrar la venta: ' . $e->getMessage());
        }
    }

    /**
     * Muestra una orden específica.
     */
    public function show(CustomerOrder $customerOrder): View
    {
        $customerOrder->load(['client.user', 'customerOrderItems.product']);
        return view('customer_orders.show', compact('customerOrder'));
    }

    /**
     * Muestra el formulario para editar una orden.
     */
    public function edit(CustomerOrder $customerOrder): View
    {
        $customerOrder->load(['client.user', 'customerOrderItems.product']);
        // Necesitarás la lista de productos y clientes si permites la edición completa
        return view('customer_orders.edit', compact('customerOrder'));
    }

    /**
     * Actualiza una orden específica en la base de datos.
     */
    public function update(Request $request, CustomerOrder $customerOrder): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled',
            'shipping_address' => 'nullable|string|max:500',
        ]);
        // Aquí podrías añadir lógica para revertir stock si se cancela un pedido
        $customerOrder->update($validated);
        return redirect()->route('customer-orders.index')->with('success', 'Estado del pedido actualizado exitosamente.');
    }

    /**
     * "Elimina" una orden. (Desactivado por seguridad)
     */
    public function destroy(CustomerOrder $customerOrder): RedirectResponse
    {
        return redirect()->route('customer-orders.index')
            ->with('error', 'La eliminación directa de pedidos no está permitida. Por favor, cambie el estado a "cancelado".');
    }

    /**
     * Maneja la exportación de las órdenes a PDF o Excel.
     */
    // public function export(Request $request)
    // {
    //     $format = $request->query('export');
    //     // Aquí iría tu lógica para generar el archivo
    //     // Por ejemplo:
    //     // if ($format === 'pdf') {
    //     //     $pdf = Pdf::loadView('exports.orders', ['orders' => $orders]);
    //     //     return $pdf->download('ordenes.pdf');
    //     // }
    //     // if ($format === 'excel') {
    //     //     return Excel::download(new CustomerOrdersExport($request->query()), 'ordenes.xlsx');
    //     // }
    //     return redirect()->back()->with('error', 'Formato de exportación no válido.');
    // }
}
