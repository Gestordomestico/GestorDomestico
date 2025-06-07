<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Category; // <-- ¡Asegúrate de que esta línea esté aquí!
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Asegúrate de importar Auth

class TransactionController extends Controller
{
    /**
     * Muestra una lista de las transacciones del usuario autenticado.
     */
    public function index()
    {
        $transactions = Auth::user()->transactions()->latest()->get(); // Obtiene las transacciones del usuario, ordenadas por las más recientes
        return view('transactions.index', compact('transactions'));
    }

    /**
     * Muestra el formulario para crear una nueva transacción.
     */
    public function create()
    {
        $categories = Auth::user()->categories()->get(); // Obtiene las categorías del usuario
        return view('transactions.create', compact('categories'));
    }
	
    /**
     * Almacena una nueva transacción en la base de datos.
     */
    public function store(Request $request)
    {
        // Validación de los datos del formulario
        $validated = $request->validate([
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:255',
            'date' => 'required|date',
			'category_id' => 'nullable|exists:categories,id', // <-- Añade validación para category_id
        ]);

        // Asigna el user_id del usuario autenticado
        $validated['user_id'] = Auth::id();

        // Valida que la categoría pertenece al usuario si se selecciona una
        if ($request->has('category_id') && $request->category_id !== null) {
            $category = Category::find($request->category_id);
            if (!$category || $category->user_id !== Auth::id()) {
                return back()->withErrors(['category_id' => 'La categoría seleccionada no es válida o no te pertenece.'])->withInput();
            }
        } else {
            $validated['category_id'] = null; // Asegura que sea null si no se selecciona
        }
		
        // Crea la transacción
        Transaction::create($validated);

        return redirect()->route('transactions.index')->with('success', 'Transacción guardada exitosamente.');
    }

    /**
     * Muestra los detalles de una transacción específica.
     * (Opcional, pero se mantiene por convención RESTful)
     */
    public function show(Transaction $transaction)
    {
        // Asegúrate de que el usuario autenticado sea el dueño de la transacción
        if ($transaction->user_id !== Auth::id()) {
            abort(403); // Acceso denegado
        }
        return view('transactions.show', compact('transaction')); // Necesitarías crear esta vista
    }

    /**
     * Muestra el formulario para editar una transacción existente.
     */
    public function edit(Transaction $transaction)
    {
        if ($transaction->user_id !== Auth::id()) {
            abort(403);
        }
        $categories = Auth::user()->categories()->get(); // Obtiene las categorías del usuario
        return view('transactions.create', compact('transaction', 'categories'));
    }
	
    /**
     * Actualiza una transacción existente en la base de datos.
     */
    public function update(Request $request, Transaction $transaction)
    {
        if ($transaction->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:255',
            'date' => 'required|date',
            'category_id' => 'nullable|exists:categories,id', // <-- Añade validación
        ]);

        // Valida que la categoría pertenece al usuario si se selecciona una
        if ($request->has('category_id') && $request->category_id !== null) {
            $category = Category::find($request->category_id);
            if (!$category || $category->user_id !== Auth::id()) {
                return back()->withErrors(['category_id' => 'La categoría seleccionada no es válida o no te pertenece.'])->withInput();
            }
        } else {
            $validated['category_id'] = null; // Asegura que sea null si no se selecciona
        }

        $transaction->update($validated);

        return redirect()->route('transactions.index')->with('success', 'Transacción actualizada exitosamente.');
    }

    /**
     * Elimina una transacción de la base de datos.
     */
    public function destroy(Transaction $transaction)
    {
        // Asegúrate de que el usuario autenticado sea el dueño de la transacción
        if ($transaction->user_id !== Auth::id()) {
            abort(403); // Acceso denegado
        }

        $transaction->delete();

        return redirect()->route('transactions.index')->with('success', 'Transacción eliminada exitosamente.');
    }
}