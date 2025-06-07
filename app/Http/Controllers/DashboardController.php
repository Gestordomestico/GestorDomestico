<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;
use App\Models\Category;
use Carbon\Carbon; // Para trabajar con fechas

class DashboardController extends Controller
{
    /**
     * Display the user's dashboard with transaction data and filters.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = $user->transactions(); // Inicia la consulta con las transacciones del usuario

        // --- Aplicar Filtros ---

        // Filtrar por rango de fechas
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        if ($startDate) {
            $query->whereDate('date', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('date', '<=', $endDate);
        }

        // Filtrar por categoría
        $categoryId = $request->input('category_id');
        if ($categoryId && $categoryId != 'all') { // 'all' para no filtrar
            $query->where('category_id', $categoryId);
        }

        // Filtrar por tipo (ingreso/gasto)
        $type = $request->input('type');
        if ($type && ($type == 'income' || $type == 'expense')) {
            $query->where('type', $type);
        }

        $transactions = $query->orderBy('date', 'asc')->get(); // Obtener las transacciones filtradas

        // --- Preparar Datos para el Gráfico ---
        // Agrupar transacciones por mes y calcular el total de ingresos y gastos
        $monthlyData = $transactions->groupBy(function($date) {
            return Carbon::parse($date->date)->format('Y-m'); // Agrupar por 'Año-Mes'
        })->map(function ($row) {
            $income = $row->where('type', 'income')->sum('amount');
            $expense = $row->where('type', 'expense')->sum('amount');
            return [
                'income' => $income,
                'expense' => $expense,
                'balance' => $income - $expense,
            ];
        })->sortBy(function($value, $key) {
            return $key; // Ordenar por mes
        });

        $chartLabels = $monthlyData->keys()->map(function($month) {
            // Formatear el mes para que sea más legible (Ej: Jun 2024)
            return Carbon::createFromFormat('Y-m', $month)->isoFormat('MMM YYYY');
        });
        $chartIncomeData = $monthlyData->pluck('income');
        $chartExpenseData = $monthlyData->pluck('expense');
        $chartBalanceData = $monthlyData->pluck('balance');

        // --- Resumen Financiero ---
        $totalIncome = $transactions->where('type', 'income')->sum('amount');
        $totalExpense = $transactions->where('type', 'expense')->sum('amount');
        $currentBalance = $totalIncome - $totalExpense;

        // --- Obtener todas las categorías para el filtro de la vista ---
        $categories = $user->categories()->orderBy('name')->get();

        // --- Lógica de Recomendaciones (Ejemplo Básico) ---
        // Esto es un ejemplo simple. En una aplicación real, esta lógica sería más compleja.
        $recommendations = [];

        if ($totalExpense > $totalIncome) {
            $recommendations[] = '¡Tus gastos superan tus ingresos! Revisa tu presupuesto e identifica áreas donde puedas reducir gastos.';
        } elseif ($totalExpense > 0.8 * $totalIncome) {
            $recommendations[] = 'Estás gastando cerca del límite de tus ingresos. Considera formas de ahorrar o aumentar tus ingresos.';
        } else {
            $recommendations[] = '¡Parece que tienes un buen control de tus finanzas! Sigue así y considera objetivos de ahorro.';
        }

        // Identificar categorías de gasto más altas (Top 3)
        $topExpenseCategories = $transactions->where('type', 'expense')
            ->groupBy('category_id')
            ->mapWithKeys(function ($group, $categoryId) use ($categories) {
                $categoryName = $categories->firstWhere('id', $categoryId)->name ?? 'Sin Categoría';
                return [$categoryName => $group->sum('amount')];
            })
            ->sortByDesc(function ($amount) {
                return $amount;
            })
            ->take(3);

        if ($topExpenseCategories->isNotEmpty()) {
            $recommendations[] = 'Tus mayores gastos se concentran en: ' . $topExpenseCategories->keys()->implode(', ') . '.';
            $recommendations[] = 'Analiza si puedes optimizar tus gastos en estas categorías.';
        }


        // Si la solicitud es AJAX (para actualizar el gráfico dinámicamente)
        if ($request->ajax()) {
            return response()->json([
                'labels' => $chartLabels,
                'incomeData' => $chartIncomeData,
                'expenseData' => $chartExpenseData,
                'balanceData' => $chartBalanceData,
                'totalIncome' => number_format($totalIncome, 2, ',', '.'),
                'totalExpense' => number_format($totalExpense, 2, ',', '.'),
                'currentBalance' => number_format($currentBalance, 2, ',', '.'),
                'recommendations' => $recommendations,
            ]);
        }

        // Si es una solicitud normal (primera carga de la página)
        return view('dashboard', [
            'chartLabels' => $chartLabels,
            'chartIncomeData' => $chartIncomeData,
            'chartExpenseData' => $chartExpenseData,
            'chartBalanceData' => $chartBalanceData,
            'totalIncome' => $totalIncome,
            'totalExpense' => $totalExpense,
            'currentBalance' => $currentBalance,
            'categories' => $categories, // Pasar categorías para el filtro
            'selectedStartDate' => $startDate,
            'selectedEndDate' => $endDate,
            'selectedCategoryId' => $categoryId,
            'selectedType' => $type,
            'recommendations' => $recommendations,
        ]);
    }
}