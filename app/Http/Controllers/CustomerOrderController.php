<?php

namespace App\Http\Controllers;

use App\Models\CustomerOrder;
use App\Models\Client; // Para seleccionar el cliente
use App\Models\Product; // Para seleccionar los productos
use App\Models\StockMovement; // Para registrar movimientos de stock
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB; // Para transacciones de base de datos
use Illuminate\Support\Facades\Auth; // Para obtener el usuario autenticado

class CustomerOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     * Muestra una lista de los recursos (pedidos de clientes).
     */
    public function index(): View
    {
        // Obtiene todos los pedidos de clientes con su cliente asociado y los ítems
        $customerOrders = CustomerOrder::with(['client.user', 'customerOrderItems.product'])
                                       ->latest()
                                       ->paginate(10);
        return view('customer_orders.index', compact('customerOrders'));
    }

    /**
     * Show the form for creating a new resource.
     * Muestra el formulario para crear un nuevo recurso (pedido de cliente).
     */
    public function create(): View
    {
        $clients = Client::with('user')->get(); // Obtiene todos los clientes para el dropdown
        $products = Product::where('active', true)->where('stock', '>', 0)->orderBy('name')->get(); // Obtiene productos activos y con stock
        return view('customer_orders.create', compact('clients', 'products'));
    }

    /**
     * Store a newly created resource in storage.
     * Almacena un nuevo recurso (pedido de cliente) en la base de datos.
     */
    public function store(Request $request): RedirectResponse
    {
        // Validar los datos del formulario
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'product_ids' => 'required|array|min:1',
            'product_ids.*' => 'exists:products,id',
            'quantities' => 'required|array|min:1',
            'quantities.*' => 'required|integer|min:1',
            'shipping_address' => 'nullable|string|max:255',
            'status' => 'required|in:pending,processing,completed,cancelled',
        ]);

        DB::beginTransaction(); // Iniciar una transacción de base de datos

        try {
            $totalAmount = 0;
            $orderItemsData = [];
            $stockMovementsData = [];

            // Preparar los ítems del pedido y verificar el stock
            foreach ($request->product_ids as $index => $productId) {
                $product = Product::find($productId);
                $quantity = (int) $request->quantities[$index];

                // Verificar stock disponible
                if (!$product || $product->stock < $quantity) {
                    DB::rollBack();
                    return redirect()->back()->withInput()->with('error', 'Stock insuficiente para el producto: ' . ($product ? $product->name : 'Desconocido'));
                }

                $orderItemsData[] = [
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'price_at_order' => $product->price, // Usar el precio actual del producto
                ];
                $totalAmount += ($quantity * $product->price);

                // Preparar los datos para el movimiento de stock
                $stockMovementsData[] = [
                    'product_id' => $productId,
                    'user_id' => Auth::id(), // El usuario logueado registra el movimiento
                    'type' => 'out', // Salida de stock
                    'quantity' => $quantity,
                    'reason' => 'Venta a cliente',
                    'created_at' => now(), // Asegurarse de tener timestamps para los movimientos
                    'updated_at' => now(),
                    // source_type y source_id se llenarán después de crear la orden
                ];
            }

            // Crear el pedido del cliente
            $customerOrder = CustomerOrder::create([
                'client_id' => $request->client_id,
                'total_amount' => $totalAmount,
                'status' => $request->status,
                'shipping_address' => $request->shipping_address,
            ]);

            // Crear los ítems del pedido y asociarlos a la orden
            foreach ($orderItemsData as $itemData) {
                $customerOrder->customerOrderItems()->create($itemData);

                // Actualizar el stock del producto
                $product = Product::find($itemData['product_id']);
                if ($product) {
                    $product->decrement('stock', $itemData['quantity']);
                }
            }

            // Crear los movimientos de stock, asociándolos a la orden creada
            foreach ($stockMovementsData as $movementData) {
                $customerOrder->stockMovement()->create(array_merge($movementData, [
                    'source_type' => $customerOrder->getMorphClass(),
                    'source_id' => $customerOrder->id,
                ]));
            }

            DB::commit(); // Confirmar la transacción
            return redirect()->route('customer-orders.index')->with('success', 'Venta registrada exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack(); // Revertir la transacción en caso de error
            return redirect()->back()->withInput()->with('error', 'Error al registrar la venta: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     * Muestra el recurso (pedido de cliente) especificado.
     */
    public function show(CustomerOrder $customerOrder): View
    {
        // Cargar las relaciones necesarias para mostrar los detalles del pedido
        $customerOrder->load(['client.user', 'customerOrderItems.product']);
        return view('customer_orders.show', compact('customerOrder'));
    }

    /**
     * Show the form for editing the specified resource.
     * Muestra el formulario para editar el recurso (pedido de cliente) especificado.
     * En este caso, solo permitiremos la edición del estado.
     */
    public function edit(CustomerOrder $customerOrder): View
    {
        $customerOrder->load(['client.user', 'customerOrderItems.product']);
        return view('customer_orders.edit', compact('customerOrder'));
    }

    /**
     * Update the specified resource in storage.
     * Actualiza el recurso (pedido de cliente) especificado en la base de datos.
     * Permite actualizar el estado del pedido.
     */
    public function update(Request $request, CustomerOrder $customerOrder): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled',
            'shipping_address' => 'nullable|string|max:255',
        ]);

        // Si el estado cambia a 'cancelled', podríamos necesitar lógica para revertir stock.
        // Esto es un tema más complejo y no lo incluiremos en este CRUD básico,
        // pero es una consideración importante para una aplicación real.
        // Por ahora, solo se actualiza el estado.

        $customerOrder->update([
            'status' => $request->status,
            'shipping_address' => $request->shipping_address,
        ]);

        return redirect()->route('customer-orders.index')->with('success', 'Estado del pedido actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     * Elimina el recurso (pedido de cliente) especificado de la base de datos.
     * NO RECOMENDADO para pedidos históricos, se prefiere "cancelar".
     */
    public function destroy(CustomerOrder $customerOrder): RedirectResponse
    {
        // La eliminación de órdenes históricas puede ser compleja por el stock.
        // No la implementaremos directamente para evitar inconsistencias en el stock.
        // Se recomienda cambiar el estado a 'cancelled' en su lugar.
        return redirect()->route('customer-orders.index')->with('error', 'La eliminación directa de pedidos históricos no está permitida para evitar inconsistencias de stock. Por favor, cambie el estado del pedido a "cancelado" si es necesario.');

        // Si realmente necesitas eliminar, la lógica sería algo así:
        /*
        DB::beginTransaction();
        try {
            // Revertir movimientos de stock asociados
            foreach ($customerOrder->stockMovements as $movement) {
                $product = Product::find($movement->product_id);
                if ($product) {
                    $product->increment('stock', $movement->quantity); // Revertir el decremento
                }
                $movement->delete(); // Eliminar el movimiento de stock
            }
            // Luego, eliminar los ítems del pedido
            $customerOrder->customerOrderItems()->delete();
            // Finalmente, eliminar el pedido
            $customerOrder->delete();

            DB::commit();
            return redirect()->route('customer-orders.index')->with('success', 'Pedido eliminado y stock revertido.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('customer-orders.index')->with('error', 'Error al eliminar el pedido: ' . $e->getMessage());
        }
        */
    }
}
