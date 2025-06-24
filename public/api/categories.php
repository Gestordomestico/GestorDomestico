<?php
// public/api/categories.php
// Ubicación: C:\laragon\www\gestor_domestico_mvp\public\api\categories.php

if (!isset($db) || !function_exists('isUserLoggedIn') || !function_exists('validateString') || !function_exists('validateType') || !function_exists('validateId')) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error interno del servidor: Dependencias críticas no cargadas.']);
    exit();
}

header('Content-Type: application/json');

// Para APIs que requieren usuario logueado, verifica aquí.
// public/index.php ya debería haber redirigido si no lo está, pero es una doble comprobación.
if (!isUserLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Acceso no autorizado. Inicia sesión.']);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents('php://input'), true);

switch ($method) {
    case 'GET': // Obtener todas las categorías (son globales para simplificar en este MVP)
        try {
            $stmt = $db->query("SELECT id, name, type FROM categories ORDER BY type, name");
            $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'categories' => $categories]);
        } catch (PDOException $e) {
            http_response_code(500);
            error_log("Error al obtener categorías: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error interno al obtener categorías.']);
        }
        break;

    case 'POST': // Añadir nueva categoría
        $name = validateString($data['name'] ?? '', 50); // Validar y sanear nombre
        $type = validateType($data['type'] ?? ''); // Validar tipo

        $errors = [];
        if ($name === false || empty($name) || strlen($name) < 2) {
            $errors[] = 'El nombre de la categoría debe tener entre 2 y 50 caracteres y no contener caracteres especiales no permitidos.';
        }
        // Expresión regular para nombres de categoría: alfanuméricos y espacios.
        if (!preg_match('/^[a-zA-Z0-9\s]+$/u', $name)) { // La 'u' para soporte UTF-8
            $errors[] = 'El nombre de la categoría solo puede contener letras, números y espacios.';
        }
        if ($type === false) {
            $errors[] = 'Tipo de categoría inválido.';
        }

        if (!empty($errors)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Errores de validación:', 'errors' => $errors]);
            exit();
        }

        try {
            // Verificar si la categoría ya existe (UNIQUE(name, type) en la tabla ya lo asegura,
            // pero esto da un mensaje de error más específico al usuario).
            $stmt = $db->prepare("SELECT COUNT(*) FROM categories WHERE name = ? AND type = ?");
            $stmt->execute([$name, $type]);
            if ($stmt->fetchColumn() > 0) {
                http_response_code(409); // Conflict
                echo json_encode(['success' => false, 'message' => 'La categoría con ese nombre y tipo ya existe.']);
                exit();
            }

            $stmt = $db->prepare("INSERT INTO categories (name, type) VALUES (?, ?)");
            $stmt->execute([$name, $type]);
            echo json_encode(['success' => true, 'message' => 'Categoría añadida con éxito.', 'id' => $db->lastInsertId()]);
        } catch (PDOException $e) {
            http_response_code(500);
            error_log("Error al añadir categoría: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error interno al añadir categoría.']);
        }
        break;

    case 'DELETE': // Eliminar categoría
        $id = validateId($_GET['id'] ?? null);

        if ($id === false) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'ID de categoría inválido.']);
            exit();
        }

        try {
            // OWASP: Considerar las implicaciones de Foreign Keys. ON DELETE CASCADE en 'transactions'
            // eliminará las transacciones asociadas a esta categoría, lo cual es el comportamiento deseado aquí.
            $stmt = $db->prepare("DELETE FROM categories WHERE id = ?");
            $stmt->execute([$id]);

            if ($stmt->rowCount() > 0) {
                echo json_encode(['success' => true, 'message' => 'Categoría eliminada con éxito.']);
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Categoría no encontrada.']);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            error_log("Error al eliminar categoría: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error interno al eliminar categoría.']);
        }
        break;

    default:
        http_response_code(405); // Method Not Allowed
        echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
        break;
}