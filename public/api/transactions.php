<?php
// public/api/transactions.php
// Ubicación: C:\laragon\www\gestor_domestico_mvp\public\api\transactions.php

// Asegurarse de que el script fue incluido por index.php y que $db y las funciones existan
if (!isset($db) || !function_exists('isUserLoggedIn') || !function_exists('isValidCategory') || !function_exists('isValidDate') || !function_exists('validateId') || !function_exists('validateType') || !function_exists('validateAmount') || !function_exists('validateString')) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error interno del servidor: Dependencias críticas no cargadas.']);
    exit();
}

header('Content-Type: application/json');

// El user_id ya está validado y disponible en la sesión gracias a public/index.php
// OWASP: Control de Acceso (Autorización) - Asegurarse que solo el usuario logueado pueda acceder a sus propias transacciones.
$user_id = $_SESSION['user_id'];

$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents('php://input'), true); // Para POST, PUT

switch ($method) {
    case 'POST': // Añadir nueva transacción
        // --- OWASP: Validación de Entrada (Añadir Transacción) ---
        $type = validateType($data['type'] ?? '');
        $category = validateString($data['category'] ?? '', 50); // Longitud máxima para categoría
        $amount = validateAmount($data['amount'] ?? '');
        $description = validateString($data['description'] ?? '', 255); // Longitud máxima para descripción
        $date = $data['date'] ?? '';

        $errors = [];
        if ($type === false) {
            $errors[] = 'Tipo de transacción inválido.';
        }
        if ($category === false || empty($category)) {
            $errors[] = 'Categoría inválida o vacía (máx. 50 caracteres).';
        } elseif (!isValidCategory($category, $type, $db)) { // Verificar si la categoría existe y coincide con el tipo
            $errors[] = 'La categoría "' . sanitizeHtmlOutput($category) . '" no existe o no es válida para el tipo seleccionado.';
        }
        if ($amount === false) {
            $errors[] = 'Monto inválido. Debe ser un número positivo con hasta 2 decimales y no exceder 99,999,999.99.';
        }
        if (!isValidDate($date)) {
            $errors[] = 'Formato de fecha inválido. Debe ser AAAA-MM-DD.';
        }
        if ($description === false) {
            $errors[] = 'La descripción es demasiado larga (máximo 255 caracteres) o contiene caracteres no permitidos.';
        }


        if (!empty($errors)) {
            http_response_code(400); // Bad Request
            echo json_encode(['success' => false, 'message' => 'Errores de validación:', 'errors' => $errors]);
            exit();
        }

        // --- OWASP: Consultas Parametrizadas (Prevención de Inyección SQL) ---
        try {
            $stmt = $db->prepare("INSERT INTO transactions (user_id, type, category, amount, description, date) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$user_id, $type, $category, $amount, $description, $date]);
            echo json_encode(['success' => true, 'message' => 'Transacción añadida con éxito.', 'id' => $db->lastInsertId()]);
        } catch (PDOException $e) {
            http_response_code(500); // Internal Server Error
            error_log("Error al añadir transacción: " . $e->getMessage()); // Registrar error en logs
            echo json_encode(['success' => false, 'message' => 'Error interno al añadir transacción.']);
        }
        break;

    case 'GET': // Obtener transacciones para el usuario logueado
        // Obtener y validar parámetros de consulta (vienen de $_GET)
        $start_date = $_GET['start_date'] ?? '';
        $end_date = $_GET['end_date'] ?? '';

        $sql = "SELECT id, type, category, amount, description, date FROM transactions WHERE user_id = ?";
        $params = [$user_id];

        if (!empty($start_date)) {
            if (!isValidDate($start_date)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Formato de fecha de inicio inválido. Use AAAA-MM-DD.']);
                exit();
            }
            $sql .= " AND date >= ?";
            $params[] = $start_date;
        }
        if (!empty($end_date)) {
            if (!isValidDate($end_date)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Formato de fecha de fin inválido. Use AAAA-MM-DD.']);
                exit();
            }
            $sql .= " AND date <= ?";
            $params[] = $end_date;
        }

        $sql .= " ORDER BY date DESC, created_at DESC";

        try {
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'transactions' => $transactions]);
        } catch (PDOException $e) {
            http_response_code(500);
            error_log("Error al obtener transacciones: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error interno al obtener transacciones.']);
        }
        break;

    case 'PUT': // Actualizar transacción
        $transaction_id = validateId($data['id'] ?? '');
        $type = validateType($data['type'] ?? '');
        $category = validateString($data['category'] ?? '', 50);
        $amount = validateAmount($data['amount'] ?? '');
        $description = validateString($data['description'] ?? '', 255);
        $date = $data['date'] ?? '';

        $errors = [];
        if ($transaction_id === false) {
            $errors[] = 'ID de transacción inválido.';
        }
        if ($type === false) {
            $errors[] = 'Tipo de transacción inválido.';
        }
        if ($category === false || empty($category)) {
            $errors[] = 'Categoría inválida o vacía (máx. 50 caracteres).';
        } elseif (!isValidCategory($category, $type, $db)) {
            $errors[] = 'La categoría "' . sanitizeHtmlOutput($category) . '" no existe o no es válida para el tipo seleccionado.';
        }
        if ($amount === false) {
            $errors[] = 'Monto inválido. Debe ser un número positivo con hasta 2 decimales y no exceder 99,999,999.99.';
        }
        if (!isValidDate($date)) {
            $errors[] = 'Formato de fecha inválido. Debe ser AAAA-MM-DD.';
        }
        if ($description === false) {
            $errors[] = 'La descripción es demasiado larga (máximo 255 caracteres) o contiene caracteres no permitidos.';
        }

        if (!empty($errors)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Errores de validación:', 'errors' => $errors]);
            exit();
        }

        try {
            // OWASP: Asegura que el usuario solo modifique sus propias transacciones (Autorización)
            $stmt = $db->prepare("UPDATE transactions SET type = ?, category = ?, amount = ?, description = ?, date = ? WHERE id = ? AND user_id = ?");
            $stmt->execute([$type, $category, $amount, $description, $date, $transaction_id, $user_id]);

            if ($stmt->rowCount() > 0) {
                echo json_encode(['success' => true, 'message' => 'Transacción actualizada con éxito.']);
            } else {
                // OWASP: No revelar si el ID no existe o si no pertenece al usuario.
                http_response_code(404); // Not Found (o Forbidden si se quiere ser más específico pero se arriesga enumeración)
                echo json_encode(['success' => false, 'message' => 'Transacción no encontrada o no tienes permiso para actualizarla.']);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            error_log("Error al actualizar transacción: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error interno al actualizar transacción.']);
        }
        break;

    case 'DELETE': // Eliminar transacción
        // Los parámetros DELETE vienen en la URL para este caso
        $transaction_id = validateId($_GET['id'] ?? '');

        if ($transaction_id === false) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'ID de transacción inválido.']);
            exit();
        }

        try {
            // OWASP: Asegura que el usuario solo elimine sus propias transacciones (Autorización)
            $stmt = $db->prepare("DELETE FROM transactions WHERE id = ? AND user_id = ?");
            $stmt->execute([$transaction_id, $user_id]);

            if ($stmt->rowCount() > 0) {
                echo json_encode(['success' => true, 'message' => 'Transacción eliminada con éxito.']);
            } else {
                http_response_code(404); // Not Found (o Forbidden)
                echo json_encode(['success' => false, 'message' => 'Transacción no encontrada o no tienes permiso para eliminarla.']);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            error_log("Error al eliminar transacción: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error interno al eliminar transacción.']);
        }
        break;

    default:
        http_response_code(405); // Method Not Allowed
        echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
        break;
}