<?php

namespace App\Http\Controllers;

use App\Models\SupplierOrder;
use App\Models\Supplier; // Para seleccionar el proveedor
use App\Models\Product; // Para seleccionar los productos
use App\Models\StockMovement; // Para registrar movimientos de stock
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB; // Para transacciones de base de datos
use Illuminate\Support\Facades\Auth; // Para obtener el usuario autenticado

class SupplierOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     * Muestra una lista de los recursos (pedidos a proveedores).
     */
    public function index(): View
    {
        // Obtiene todos los pedidos a proveedores con su proveedor asociado y los ítems
        $supplierOrders = SupplierOrder::with(['supplier', 'supplierOrderItems.product'])
                                       ->latest()
                                       ->paginate(10);
        return view('supplier_orders.index', compact('supplierOrders'));
    }

    /**
     * Show the form for creating a new resource.
     * Muestra el formulario para crear un nuevo recurso (pedido a proveedor).
     */
    public function create(): View
    {
        $suppliers = Supplier::all(); // Obtiene todos los proveedores para el dropdown
        $products = Product::where('is_active', true)->orderBy('name')->get(); // Obtiene todos los productos activos
        return view('supplier_orders.create', compact('suppliers', 'products'));
    }

    /**
     * Store a newly created resource in storage.
     * Almacena un nuevo recurso (pedido a proveedor) en la base de datos.
     */
    public function store(Request $request): RedirectResponse
    {
        // Valida los datos del formulario
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'product_ids' => 'required|array|min:1',
            'product_ids.*' => 'exists:products,id',
            'quantities' => 'required|array|min:1',
            'quantities.*' => 'required|integer|min:1',
            'cost_at_order.*' => 'required|numeric|min:0', // Costo por producto
            'order_date' => 'nullable|date',
            'expected_delivery_date' => 'nullable|date|after_or_equal:order_date',
            'status' => 'required|in:pending,ordered,received,cancelled',
        ]);

        DB::beginTransaction(); // Iniciar una transacción de base de datos

        try {
            $totalAmount = 0;
            $orderItemsData = [];
            $stockMovementsData = [];

            // Preparar los ítems del pedido
            foreach ($request->product_ids as $index => $productId) {
                $product = Product::find($productId);
                $quantity = (int) $request->quantities[$index];
                $cost = (float) $request->cost_at_order[$index];

                if (!$product) {
                    DB::rollBack();
                    return redirect()->back()->withInput()->with('error', 'Producto no encontrado: ID ' . $productId);
                }

                $orderItemsData[] = [
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'cost_at_order' => $cost,
                ];
                $totalAmount += ($quantity * $cost);

                // Si el estado es 'received' al crear, también genera movimiento de stock
                if ($request->status === 'received') {
                    $stockMovementsData[] = [
                        'product_id' => $productId,
                        'user_id' => Auth::id(), // El usuario logueado registra el movimiento
                        'type' => 'in', // Entrada de stock
                        'quantity' => $quantity,
                        'reason' => 'Compra a proveedor (recepción inicial)',
                        'created_at' => now(),
                        'updated_at' => now(),
                        // source_type y source_id se llenarán después de crear la orden
                    ];
                }
            }

            // Crear el pedido al proveedor
            $supplierOrder = SupplierOrder::create([
                'supplier_id' => $request->supplier_id,
                'total_amount' => $totalAmount,
                'status' => $request->status,
                'order_date' => $request->order_date,
                'expected_delivery_date' => $request->expected_delivery_date,
            ]);

            // Crear los ítems del pedido y asociarlos a la orden
            foreach ($orderItemsData as $itemData) {
                $supplierOrder->supplierOrderItems()->create($itemData);

                // Si el estado es 'received' al crear, actualiza el stock del producto
                if ($request->status === 'received') {
                    $product = Product::find($itemData['product_id']);
                    if ($product) {
                        $product->increment('stock', $itemData['quantity']);
                    }
                }
            }

            // Crear los movimientos de stock si la orden fue recibida
            foreach ($stockMovementsData as $movementData) {
                $supplierOrder->stockMovement()->create(array_merge($movementData, [
                    'source_type' => $supplierOrder->getMorphClass(),
                    'source_id' => $supplierOrder->id,
                ]));
            }

            DB::commit(); // Confirmar la transacción
            return redirect()->route('supplier-orders.index')->with('success', 'Pedido a proveedor registrado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack(); // Revertir la transacción en caso de error
            return redirect()->back()->withInput()->with('error', 'Error al registrar el pedido: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     * Muestra el recurso (pedido a proveedor) especificado.
     */
    public function show(SupplierOrder $supplierOrder): View
    {
        // Cargar las relaciones necesarias para mostrar los detalles del pedido
        $supplierOrder->load(['supplier', 'supplierOrderItems.product']);
        return view('supplier_orders.show', compact('supplierOrder'));
    }

    /**
     * Show the form for editing the specified resource.
     * Muestra el formulario para editar el recurso (pedido a proveedor) especificado.
     * Solo permite la edición del estado y fechas, no de ítems de pedido.
     */
    public function edit(SupplierOrder $supplierOrder): View
    {
        $supplierOrder->load(['supplier', 'supplierOrderItems.product']);
        $products = Product::where('is_active', true)->orderBy('name')->get(); // Para referencia, aunque no se editen ítems aquí
        return view('supplier_orders.edit', compact('supplierOrder', 'products'));
    }

    /**
     * Update the specified resource in storage.
     * Actualiza el recurso (pedido a proveedor) especificado en la base de datos.
     * Permite actualizar el estado del pedido y fechas.
     */
    public function update(Request $request, SupplierOrder $supplierOrder): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:pending,ordered,received,cancelled',
            'order_date' => 'nullable|date',
            'expected_delivery_date' => 'nullable|date|after_or_equal:order_date',
        ]);

        DB::beginTransaction();
        try {
            // Lógica para actualizar el stock solo si el estado cambia a 'received'
            // y no estaba ya en 'received' (para evitar doble conteo)
            if ($request->status === 'received' && $supplierOrder->status !== 'received') {
                foreach ($supplierOrder->supplierOrderItems as $item) {
                    $product = Product::find($item->product_id);
                    if ($product) {
                        $product->increment('stock', $item->quantity); // Incrementa el stock
                    }

                    // Registrar el movimiento de stock de 'in'
                    StockMovement::create([
                        'product_id' => $item->product_id,
                        'user_id' => Auth::id(),
                        'type' => 'in',
                        'quantity' => $item->quantity,
                        'reason' => 'Recepción de pedido a proveedor',
                        'source_type' => $supplierOrder->getMorphClass(),
                        'source_id' => $supplierOrder->id,
                    ]);
                }
            }
            // Lógica para revertir stock si el estado cambia de 'received' a otro
            else if ($request->status !== 'received' && $supplierOrder->status === 'received') {
                foreach ($supplierOrder->supplierOrderItems as $item) {
                    $product = Product::find($item->product_id);
                    if ($product) {
                        // Asegurarse de que el stock no sea negativo
                        $product->decrement('stock', $item->quantity); // Decrementa el stock (revierte)
                    }

                    // Opcional: Eliminar o marcar como revertido el movimiento de stock original
                    // O crear un nuevo movimiento 'out' con razón 'Reversión de compra'
                    StockMovement::create([
                        'product_id' => $item->product_id,
                        'user_id' => Auth::id(),
                        'type' => 'out', // Salida de stock (reversión)
                        'quantity' => $item->quantity,
                        'reason' => 'Reversión de recepción de pedido a proveedor',
                        'source_type' => $supplierOrder->getMorphClass(),
                        'source_id' => $supplierOrder->id,
                    ]);
                }
            }


            $supplierOrder->update($request->except(['product_ids', 'quantities', 'cost_at_order'])); // Actualiza solo los campos permitidos

            DB::commit();
            return redirect()->route('supplier-orders.index')->with('success', 'Pedido a proveedor actualizado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Error al actualizar el pedido: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     * Elimina el recurso (pedido a proveedor) especificado de la base de datos.
     * NO RECOMENDADO para pedidos históricos, se prefiere "cancelar".
     */
    public function destroy(SupplierOrder $supplierOrder): RedirectResponse
    {
        // La eliminación de órdenes históricas puede ser compleja por el stock.
        // No la implementaremos directamente para evitar inconsistencias en el stock.
        // Se recomienda cambiar el estado a 'cancelled' en su lugar.
        return redirect()->route('supplier-orders.index')->with('error', 'La eliminación directa de pedidos históricos no está permitida para evitar inconsistencias de stock. Por favor, cambie el estado del pedido a "cancelado" si es necesario.');
    }
}
