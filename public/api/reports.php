<?php
// public/api/reports.php

require_once '../../config.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit();
}

if (!isAuthenticated()) {
    jsonResponse(['error' => 'Acceso no autorizado. Inicia sesión.'], 401);
}

$user_id = $_SESSION['user_id'];

try {
    $stmt_income = $pdo->prepare("SELECT SUM(amount) FROM transactions WHERE type = 'income' AND user_id = ?");
    $stmt_income->execute([$user_id]);
    $total_income = $stmt_income->fetchColumn() ?: 0;

    $stmt_expense = $pdo->prepare("SELECT SUM(amount) FROM transactions WHERE type = 'expense' AND user_id = ?");
    $stmt_expense->execute([$user_id]);
    $total_expense = $stmt_expense->fetchColumn() ?: 0;

    $balance = $total_income - $total_expense;

    $stmt_expense_by_category = $pdo->prepare("SELECT category, SUM(amount) as total FROM transactions WHERE type = 'expense' AND user_id = ? GROUP BY category ORDER BY total DESC");
    $stmt_expense_by_category->execute([$user_id]);
    $expense_by_category = $stmt_expense_by_category->fetchAll();

    $stmt_income_by_category = $pdo->prepare("SELECT category, SUM(amount) as total FROM transactions WHERE type = 'income' AND user_id = ? GROUP BY category ORDER BY total DESC");
    $stmt_income_by_category->execute([$user_id]);
    $income_by_category = $stmt_income_by_category->fetchAll();

    jsonResponse([
        'summary' => [
            'total_income' => round($total_income, 2),
            'total_expense' => round($total_expense, 2),
            'balance' => round($balance, 2)
        ],
        'expense_by_category' => $expense_by_category,
        'income_by_category' => $income_by_category
    ]);

} catch (PDOException $e) {
    error_log("Error en la base de datos (reports.php): " . $e->getMessage());
    jsonResponse(['error' => 'Error en la base de datos para reportes: ' . $e->getMessage()], 500);
} catch (Exception $e) {
    error_log("Error interno del servidor (reports.php): " . $e->getMessage());
    jsonResponse(['error' => 'Error interno del servidor para reportes: ' . $e->getMessage()], 500);
}
?>