<?php
// public/api/reports.php
// Ubicación: C:\laragon\www\gestor_domestico_mvp\public\api\reports.php

// Asegurarse de que el script fue incluido por index.php y que $db y las funciones existan
if (!isset($db) || !function_exists('isUserLoggedIn') || !function_exists('isValidDate') || !function_exists('validateType')) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error interno del servidor: Dependencias críticas no cargadas.']);
    exit();
}

header('Content-Type: application/json');

// El user_id ya está validado y disponible en la sesión gracias a public/index.php
$user_id = $_SESSION['user_id'];

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'summary':
        // Obtener y validar parámetros de consulta (vienen de $_GET)
        $start_date = $_GET['start_date'] ?? '';
        $end_date = $_GET['end_date'] ?? '';

        $errors = [];
        if (!empty($start_date) && !isValidDate($start_date)) {
            $errors[] = 'Formato de fecha de inicio inválido. Use AAAA-MM-DD.';
        }
        if (!empty($end_date) && !isValidDate($end_date)) {
            $errors[] = 'Formato de fecha de fin inválido. Use AAAA-MM-DD.';
        }

        if (!empty($errors)) {
            http_response_code(400); // Bad Request
            echo json_encode(['success' => false, 'message' => 'Errores de validación:', 'errors' => $errors]);
            exit();
        }

        $sql = "SELECT
                    SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) AS total_income,
                    SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) AS total_expense
                FROM transactions
                WHERE user_id = ?";
        $params = [$user_id];

        if (!empty($start_date)) {
            $sql .= " AND date >= ?";
            $params[] = $start_date;
        }
        if (!empty($end_date)) {
            $sql .= " AND date <= ?";
            $params[] = $end_date;
        }

        try {
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            $total_income = (float)($result['total_income'] ?? 0);
            $total_expense = (float)($result['total_expense'] ?? 0);
            $balance = $total_income - $total_expense;

            echo json_encode([
                'success' => true,
                'summary' => [
                    'total_income' => $total_income,
                    'total_expense' => $total_expense,
                    'balance' => $balance
                ]
            ]);

        } catch (PDOException $e) {
            http_response_code(500);
            error_log("Error al obtener resumen: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error interno al obtener el resumen.']);
        }
        break;

    case 'category_summary':
        // Obtener el resumen de gastos/ingresos por categoría
        $type = $_GET['type'] ?? null; // 'income' or 'expense'
        $start_date = $_GET['start_date'] ?? '';
        $end_date = $_GET['end_date'] ?? '';

        $errors = [];
        if ($type !== null && validateType($type) === false) { // Permitir null para ambos tipos, o validar
            $errors[] = 'Tipo de reporte por categoría inválido.';
        }
        if (!empty($start_date) && !isValidDate($start_date)) {
            $errors[] = 'Formato de fecha de inicio inválido.';
        }
        if (!empty($end_date) && !isValidDate($end_date)) {
            $errors[] = 'Formato de fecha de fin inválido.';
        }

        if (!empty($errors)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Errores de validación:', 'errors' => $errors]);
            exit();
        }

        $sql = "SELECT category, SUM(amount) AS total_amount
                FROM transactions
                WHERE user_id = ?";
        $params = [$user_id];

        if ($type && validateType($type)) { // Solo añadir filtro de tipo si es válido y está presente
            $sql .= " AND type = ?";
            $params[] = $type;
        }
        if (!empty($start_date)) {
            $sql .= " AND date >= ?";
            $params[] = $start_date;
        }
        if (!empty($end_date)) {
            $sql .= " AND date <= ?";
            $params[] = $end_date;
        }

        $sql .= " GROUP BY category ORDER BY total_amount DESC";

        try {
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            $category_summary = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode([
                'success' => true,
                'category_summary' => $category_summary
            ]);

        } catch (PDOException $e) {
            http_response_code(500);
            error_log("Error al obtener resumen por categoría: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error interno al obtener el resumen por categoría.']);
        }
        break;

    default:
        http_response_code(400); // Bad Request
        echo json_encode(['success' => false, 'message' => 'Acción de reporte no válida.']);
        break;
}