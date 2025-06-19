<?php
// public/api/transactions.php

require_once '../../config.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
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
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // Obtener transacciones del usuario actual
        $stmt = $pdo->prepare("SELECT * FROM transactions WHERE user_id = ? ORDER BY date DESC, id DESC");
        $stmt->execute([$user_id]);
        $transactions = $stmt->fetchAll();
        jsonResponse($transactions);

    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);

        $type = filter_var($input['type'] ?? '', FILTER_SANITIZE_STRING);
        $category = filter_var($input['category'] ?? '', FILTER_SANITIZE_STRING);
        $amount = filter_var($input['amount'] ?? '', FILTER_VALIDATE_FLOAT);
        $description = filter_var($input['description'] ?? '', FILTER_SANITIZE_STRING);
        $date = filter_var($input['date'] ?? '', FILTER_SANITIZE_STRING);

        if (empty($type) || empty($category) || $amount === false || $amount <= 0 || empty($date)) {
            jsonResponse(['error' => 'Datos incompletos o inválidos. Monto debe ser positivo.'], 400);
        }
        if (!in_array($type, ['income', 'expense'])) {
            jsonResponse(['error' => 'Tipo de transacción inválido. Debe ser "income" o "expense".'], 400);
        }

        // Validar que la categoría exista y sea del tipo correcto
        $stmt_check_category = $pdo->prepare("SELECT COUNT(*) FROM categories WHERE name = ? AND type = ?");
        $stmt_check_category->execute([$category, $type]);
        if ($stmt_check_category->fetchColumn() === 0) {
            jsonResponse(['error' => 'Categoría inválida o no existente para el tipo seleccionado.'], 400);
        }

        $stmt = $pdo->prepare("INSERT INTO transactions (user_id, type, category, amount, description, date) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $type, $category, $amount, $description, $date]);

        jsonResponse(['message' => 'Transacción guardada con éxito', 'id' => $pdo->lastInsertId()], 201);

    } else {
        jsonResponse(['error' => 'Método no permitido.'], 405);
    }
} catch (PDOException $e) {
    error_log("Error en la base de datos (transactions.php): " . $e->getMessage());
    jsonResponse(['error' => 'Error en la base de datos: ' . $e->getMessage()], 500);
} catch (Exception $e) {
    error_log("Error interno del servidor (transactions.php): " . $e->getMessage());
    jsonResponse(['error' => 'Error interno del servidor: ' . $e->getMessage()], 500);
}
?>