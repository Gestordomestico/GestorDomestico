<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Importa Auth

class CategoryController extends Controller
{
    /**
     * Muestra una lista de las categorías del usuario autenticado.
     */
    public function index()
    {
        $categories = Auth::user()->categories()->latest()->get(); // Obtiene las categorías del usuario
        return view('categories.index', compact('categories'));
    }

    /**
     * Muestra el formulario para crear una nueva categoría.
     */
    public function create()
    {
        return view('categories.create');
    }

    /**
     * Almacena una nueva categoría en la base de datos.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,NULL,id,user_id,' . Auth::id(), // Nombre único por usuario
            'type' => 'required|in:income,expense',
        ]);

        $validated['user_id'] = Auth::id();

        Category::create($validated);

        return redirect()->route('categories.index')->with('success', 'Categoría guardada exitosamente.');
    }

    /**
     * Muestra los detalles de una categoría específica (opcional).
     */
    public function show(Category $category)
    {
        if ($category->user_id !== Auth::id()) {
            abort(403); // Acceso denegado
        }
        return view('categories.show', compact('category')); // Necesitarías crear esta vista
    }

    /**
     * Muestra el formulario para editar una categoría existente.
     */
    public function edit(Category $category)
    {
        if ($category->user_id !== Auth::id()) {
            abort(403); // Acceso denegado
        }
        return view('categories.create', compact('category')); // Reutiliza la vista 'create'
    }

    /**
     * Actualiza una categoría existente en la base de datos.
     */
    public function update(Request $request, Category $category)
    {
        if ($category->user_id !== Auth::id()) {
            abort(403); // Acceso denegado
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id . ',id,user_id,' . Auth::id(), // Nombre único por usuario, ignorando la categoría actual
            'type' => 'required|in:income,expense',
        ]);

        $category->update($validated);

        return redirect()->route('categories.index')->with('success', 'Categoría actualizada exitosamente.');
    }

    /**
     * Elimina una categoría de la base de datos.
     */
    public function destroy(Category $category)
    {
        if ($category->user_id !== Auth::id()) {
            abort(403); // Acceso denegado
        }

        // Opcional: Manejar transacciones asociadas. Por ahora, las transacciones se quedarán sin categoría.
        // Si quieres que las transacciones asociadas se eliminen o se reasignen a una categoría por defecto,
        // necesitarías añadir lógica aquí antes de $category->delete().
        // Por ejemplo:
        // $category->transactions()->update(['category_id' => null]); // Desvincula transacciones
        // O bien, si tienes una categoría "Sin Categoría":
        // $defaultCategory = Auth::user()->categories()->where('name', 'Sin Categoría')->first();
        // $category->transactions()->update(['category_id' => $defaultCategory->id]);

        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Categoría eliminada exitosamente.');
    }
}