<?php
// public/api/auth.php

require_once '../../config.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit();
}

$action = $_GET['action'] ?? '';
$input = json_decode(file_get_contents('php://input'), true);

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = filter_var($input['username'] ?? '', FILTER_SANITIZE_STRING);
        $password = $input['password'] ?? '';

        if (empty($username) || empty($password)) {
            jsonResponse(['error' => 'Usuario y contraseña son requeridos.'], 400);
        }

        if ($action === 'register') {
            if (strlen($username) < 3 || strlen($username) > 50) {
                jsonResponse(['error' => 'El nombre de usuario debe tener entre 3 y 50 caracteres.'], 400);
            }
            $hashed_password = hashPassword($password);

            $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            try {
                $stmt->execute([$username, $hashed_password]);
                jsonResponse(['message' => 'Usuario registrado con éxito. Ahora puedes iniciar sesión.'], 201);
            } catch (PDOException $e) {
                if (strpos($e->getMessage(), 'UNIQUE constraint failed') !== false) {
                    jsonResponse(['error' => 'El nombre de usuario ya existe.'], 409);
                } else {
                    error_log("Error al registrar usuario: " . $e->getMessage());
                    jsonResponse(['error' => 'Error interno del servidor al registrar usuario.'], 500);
                }
            }
        } elseif ($action === 'login') {
            $stmt = $pdo->prepare("SELECT id, username, password FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if ($user && verifyPassword($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                jsonResponse(['message' => 'Inicio de sesión exitoso.', 'username' => $user['username']], 200);
            } else {
                jsonResponse(['error' => 'Credenciales inválidas.'], 401);
            }
        } else {
            jsonResponse(['error' => 'Acción no válida.'], 400);
        }
    } elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'logout') {
        session_unset();
        session_destroy();
        setcookie(session_name(), '', time() - 3600, '/');
        jsonResponse(['message' => 'Sesión cerrada con éxito.'], 200);
    } else {
        jsonResponse(['error' => 'Método o acción no permitida.'], 405);
    }
} catch (Exception $e) {
    error_log("Error general en auth.php: " . $e->getMessage());
    jsonResponse(['error' => 'Error del servidor: ' . $e->getMessage()], 500);
}
?>