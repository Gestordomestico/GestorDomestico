<?php
// public/api/auth.php
// Ubicación: C:\laragon\www\gestor_domestico_mvp\public\api\auth.php

// session_start() NO es necesario aquí, ya que public/index.php ya lo hace.

// Asegurarse de que el script fue incluido por index.php y $db existe
if (!isset($db) || !function_exists('sessionSecurityCheck')) { // Asegurar funciones vitales
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error interno del servidor: Dependencias no cargadas.']);
    exit();
}

header('Content-Type: application/json'); // La API siempre devuelve JSON

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

$username = $input['username'] ?? '';
$password = $input['password'] ?? '';
$action = $input['action'] ?? ''; // Para distinguir entre login/register/logout

$errors = [];

if ($method === 'POST') {
    if ($action === 'register') {
        // --- OWASP: Validación de Entrada (Registro) ---
        // Validación de longitud y formato de usuario
        $sanitized_username = validateString($username, 50); // Usar función de validación
        if ($sanitized_username === false || empty($sanitized_username) || strlen($sanitized_username) < 3) {
            $errors[] = 'El nombre de usuario debe tener entre 3 y 50 caracteres.';
        }
        // Expresión regular más estricta: solo letras minúsculas/mayúsculas, números, guion bajo, sin espacios.
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $sanitized_username)) {
            $errors[] = 'El nombre de usuario solo puede contener letras (A-Z, a-z), números (0-9) y guiones bajos (_).';
        }

        // Validación de complejidad de contraseña (ya implementada y mejorada)
        if (empty($password) || strlen($password) < 8) {
            $errors[] = 'La contraseña debe tener al menos 8 caracteres.';
        }
        // Expresión regular para complejidad de contraseña (mínimo 1 mayúscula, 1 minúscula, 1 número, 1 caracter especial)
        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password)) {
            $errors[] = 'La contraseña debe contener al menos una mayúscula, una minúscula, un número y un carácter especial (@$!%*?&).';
        }

        if (!empty($errors)) {
            http_response_code(400); // Bad Request
            echo json_encode(['success' => false, 'message' => 'Errores de validación en registro:', 'errors' => $errors]);
            exit();
        }

        try {
            // Comprobar si el nombre de usuario ya existe
            $stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
            $stmt->execute([$sanitized_username]);
            if ($stmt->fetchColumn() > 0) {
                http_response_code(409); // Conflict
                echo json_encode(['success' => false, 'message' => 'El nombre de usuario ya existe. Por favor, elige otro.']);
                exit();
            }

            // OWASP: Hash Seguro de Contraseña (BCRYPT es fuerte y maneja salting automáticamente)
            $hashed_password = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
            if ($hashed_password === false) {
                 throw new Exception("Error al hashear la contraseña.");
            }

            $stmt = $db->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            $stmt->execute([$sanitized_username, $hashed_password]);

            echo json_encode(['success' => true, 'message' => 'Registro exitoso. Ahora puedes iniciar sesión.']);

        } catch (PDOException $e) {
            http_response_code(500); // Internal Server Error
            error_log("Error de registro PDO: " . $e->getMessage()); // Registrar en logs
            echo json_encode(['success' => false, 'message' => 'Error en el registro. Por favor, inténtalo de nuevo más tarde.']);
        } catch (Exception $e) {
            http_response_code(500);
            error_log("Error de registro (hashing): " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error interno del servidor.']);
        }
        exit();

    } elseif ($action === 'login') {
        // --- OWASP: Validación de Entrada (Inicio de Sesión) ---
        // Se aplican validaciones de longitud mínima/máxima para prevenir ataques de denegación de servicio o manipulación.
        if (empty($username) || strlen($username) < 3 || strlen($username) > 50) {
            $errors[] = 'El nombre de usuario es requerido y debe tener entre 3 y 50 caracteres.';
        }
        if (empty($password)) {
            $errors[] = 'La contraseña es requerida.';
        }

        if (!empty($errors)) {
            http_response_code(400); // Bad Request
            echo json_encode(['success' => false, 'message' => 'Errores de validación en login:', 'errors' => $errors]);
            exit();
        }

        try {
            $stmt = $db->prepare("SELECT id, password FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // OWASP: Protección contra ataques de fuerza bruta y de temporización (password_verify es seguro)
            // Se usa un mensaje genérico para prevenir enumeración de usuarios.
            if ($user && password_verify($password, $user['password'])) {
                // Re-hasheo de contraseña si se usa un algoritmo o costo antiguo (para mantener las contraseñas actualizadas)
                if (password_needs_rehash($user['password'], PASSWORD_BCRYPT, ['cost' => 12])) {
                    $new_hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
                    if ($new_hash !== false) {
                        $update_stmt = $db->prepare("UPDATE users SET password = ? WHERE id = ?");
                        $update_stmt->execute([$new_hash, $user['id']]);
                    } else {
                        error_log("Fallo al re-hashear la contraseña para el usuario ID: " . $user['id']);
                    }
                }

                // OWASP: Gestión de Sesiones - Establecer variables de sesión
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $username;
                $_SESSION['LAST_ACTIVITY'] = time(); // Establecer tiempo de última actividad para la expiración
                $_SESSION['HTTP_USER_AGENT'] = md5($_SERVER['HTTP_USER_AGENT'] ?? ''); // Almacenar hash del agente de usuario para detección de secuestro

                // OWASP: Gestión de Sesiones - Regenerar ID de sesión al iniciar sesión con éxito
                session_regenerate_id(true);

                echo json_encode(['success' => true, 'message' => 'Inicio de sesión exitoso.']);
            } else {
                // OWASP: Prevención de Enumeración - Mensaje de error genérico
                http_response_code(401); // Unauthorized
                echo json_encode(['success' => false, 'message' => 'Nombre de usuario o contraseña incorrectos.']);
            }

        } catch (PDOException $e) {
            http_response_code(500); // Internal Server Error
            error_log("Error de login PDO: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error en el inicio de sesión. Por favor, inténtalo de nuevo más tarde.']);
        }
        exit();

    } elseif ($action === 'logout') {
        // --- OWASP: Terminación de Sesión ---
        session_unset();     // Desestablecer todas las variables de sesión
        session_destroy();   // Destruir la sesión
        // Borrar la cookie de sesión del navegador
        setcookie(session_name(), '', time() - 3600, '/');

        echo json_encode(['success' => true, 'message' => 'Sesión cerrada exitosamente.']);
        exit();

    } else {
        http_response_code(400); // Bad Request
        echo json_encode(['success' => false, 'message' => 'Acción no válida.']);
        exit();
    }
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
    exit();
}