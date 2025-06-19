<?php
// public/api/categories.php

require_once '../../config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit();
}

if (!isAuthenticated()) {
    jsonResponse(['error' => 'Acceso no autorizado. Inicia sesión.'], 401);
}

try {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $stmt = $pdo->query("SELECT id, name, type FROM categories ORDER BY type ASC, name ASC");
        $categories = $stmt->fetchAll();
        jsonResponse($categories);

    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);

        $name = filter_var($input['name'] ?? '', FILTER_SANITIZE_STRING);
        $type = filter_var($input['type'] ?? '', FILTER_SANITIZE_STRING);

        if (empty($name) || empty($type)) {
            jsonResponse(['error' => 'Nombre y tipo de categoría son requeridos.'], 400);
        }
        if (!in_array($type, ['income', 'expense'])) {
            jsonResponse(['error' => 'Tipo de categoría inválido. Debe ser "income" o "expense".'], 400);
        }

        $name = ucwords(strtolower(trim($name)));

        $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM categories WHERE LOWER(name) = LOWER(?) AND type = ?");
        $stmt_check->execute([$name, $type]);
        if ($stmt_check->fetchColumn() > 0) {
            jsonResponse(['error' => 'La categoría "' . $name . '" de tipo "' . $type . '" ya existe.'], 409);
        }

        $stmt = $pdo->prepare("INSERT INTO categories (name, type) VALUES (?, ?)");
        $stmt->execute([$name, $type]);
        jsonResponse(['message' => 'Categoría "' . $name . '" agregada con éxito.', 'id' => $pdo->lastInsertId()], 201);

    } elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
        $id = filter_var($_GET['id'] ?? '', FILTER_VALIDATE_INT);

        if (!$id) {
            jsonResponse(['error' => 'ID de categoría inválido.'], 400);
        }

        $stmt_check_usage = $pdo->prepare("SELECT COUNT(*) FROM transactions WHERE category = (SELECT name FROM categories WHERE id = ?)");
        $stmt_check_usage->execute([$id]);
        if ($stmt_check_usage->fetchColumn() > 0) {
            jsonResponse(['error' => 'Esta categoría está en uso por transacciones y no puede ser eliminada. Elimine las transacciones asociadas primero.'], 409);
        }

        $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
        $stmt->execute([$id]);

        if ($stmt->rowCount() > 0) {
            jsonResponse(['message' => 'Categoría eliminada con éxito.'], 200);
        } else {
            jsonResponse(['error' => 'Categoría no encontrada.'], 404);
        }

    } else {
        jsonResponse(['error' => 'Método no permitido.'], 405);
    }
} catch (PDOException $e) {
    error_log("Error en la base de datos (categories.php): " . $e->getMessage());
    jsonResponse(['error' => 'Error en la base de datos: ' . $e->getMessage()], 500);
} catch (Exception $e) {
    error_log("Error interno del servidor (categories.php): " . $e->getMessage());
    jsonResponse(['error' => 'Error interno del servidor: ' . $e->getMessage()], 500);
}
?>