<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\User; // Necesitamos el modelo User para crear el usuario asociado
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash; // Para hashear la contraseña

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     * Muestra una lista de los recursos (clientes).
     */
    public function index(Request $request): View
    {
        $search = $request->get('search'); // Obtiene el término de búsqueda de la solicitud    
        
        $clients = Client::with('user')
        ->when($search, function ($query) use ($search) {
            return $query->where(function ($q) use ($search) {
                $q->where('phone', 'like', '%' . $search . '%')
                  ->orWhere('dni', 'like', '%' . $search . '%')
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->where('name', 'like', '%' . $search . '%')
                               ->orWhere('email', 'like', '%' . $search . '%');
                  });
            });
        })
        ->paginate(10);
    
    return view('clients.index', compact('clients')); // Pasa los clientes a la vista
    }

    /**
     * Show the form for creating a new resource.
     * Muestra el formulario para crear un nuevo recurso (cliente).
     */
    public function create(): View
    {
        return view('clients.create');
    }

    /**
     * Store a newly created resource in storage.
     * Almacena un nuevo recurso (cliente) en la base de datos.
     */
    public function store(Request $request): RedirectResponse
    {
        // Valida los datos del formulario, incluyendo los del usuario asociado
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email', // Email debe ser único en la tabla users
            'password' => 'required|string|min:8|confirmed', // Contraseña y confirmación
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'dni' => 'nullable|string|max:255|unique:clients,dni', // DNI único en la tabla clients
        ]);

        // Crea el usuario primero
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'client', // Asigna el rol de cliente
        ]);

        // Luego crea el cliente asociado al usuario
        $user->client()->create([
            'address' => $request->address,
            'phone' => $request->phone,
            'dni' => $request->dni,
        ]);

        // Redirige de vuelta al índice de clientes con un mensaje de éxito
        return redirect()->route('clients.index')->with('success', 'Cliente creado exitosamente.');
    }

    /**
     * Display the specified resource.
     * Muestra el recurso (cliente) especificado.
     */
    public function show(Client $client): RedirectResponse
    {
        return redirect()->route('clients.edit', $client);
    }

    /**
     * Show the form for editing the specified resource.
     * Muestra el formulario para editar el recurso (cliente) especificado.
     */
    public function edit(Client $client): View
    {
        // Carga el usuario asociado al cliente
        $client->load('user');
        return view('clients.edit', compact('client'));
    }

    /**
     * Update the specified resource in storage.
     * Actualiza el recurso (cliente) especificado en la base de datos.
     */
    public function update(Request $request, Client $client): RedirectResponse
    {
        // Valida los datos del formulario
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $client->user_id, // Email único ignorando el ID del usuario actual
            'password' => 'nullable|string|min:8|confirmed', // Contraseña opcional y confirmación
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'dni' => 'nullable|string|max:255|unique:clients,dni,' . $client->id, // DNI único ignorando el ID del cliente actual
        ]);

        // Actualiza el usuario asociado
        $client->user->update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password ? Hash::make($request->password) : $client->user->password, // Actualiza contraseña solo si se proporciona
        ]);

        // Actualiza la información del cliente
        $client->update([
            'address' => $request->address,
            'phone' => $request->phone,
            'dni' => $request->dni,
        ]);

        // Redirige de vuelta al índice de clientes con un mensaje de éxito
        return redirect()->route('clients.index')->with('success', 'Cliente actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     * Elimina el recurso (cliente) especificado de la base de datos.
     */
    public function destroy(Client $client): RedirectResponse
    {
        try {
            // Eliminar el usuario asociado, lo que en cascada eliminará el cliente (onDelete('cascade') en la migración)
            $client->user->delete();
            return redirect()->route('clients.index')->with('success', 'Cliente eliminado exitosamente.');
        } catch (\Illuminate\Database\QueryException $e) {
            // Esto es importante si el cliente tiene pedidos históricos
            return redirect()->route('clients.index')->with('error', 'No se puede eliminar el cliente porque tiene pedidos asociados.');
        }
    }
}
