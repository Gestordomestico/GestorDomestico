<?php
// public/index.php
// Ubicación: C:\laragon\www\gestor_domestico_mvp\public\index.php

// Iniciar la sesión al principio de todo
session_start();

// Incluir archivos de configuración y funciones
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../functions.php';

// Ejecutar el chequeo de seguridad de sesión en cada solicitud
sessionSecurityCheck();

// Obtener la ruta de la URL solicitada
$request_uri = strtok($_SERVER['REQUEST_URI'] ?? '/', '?'); // Eliminar parámetros de consulta y asegurar un valor por defecto

// Asegúrate de que BASE_URL esté definido en config.php
if (!defined('BASE_URL')) {
    http_response_code(500);
    echo "Error de configuración: BASE_URL no está definido.";
    exit();
}

$base_path = BASE_URL;

// Eliminar el BASE_URL para obtener la ruta interna (ej: '/login', '/api/auth')
if (strpos($request_uri, $base_path) === 0) {
    $path = substr($request_uri, strlen($base_path));
} else {
    // Si la BASE_URL no coincide, podría ser un acceso directo o error de configuración
    // En este caso, asumimos el root del public folder.
    $path = $request_uri;
}

// Limpiar la ruta para evitar problemas con barras dobles o finales
$path = rtrim($path, '/');
if (empty($path)) {
    $path = '/';
}

// Rutas de la aplicación
switch ($path) {
    case '/':
    case '/dashboard':
        // Si el usuario no está logueado, redirigir a la página de login
        if (!isUserLoggedIn()) {
            header('Location: ' . BASE_URL . '/login');
            exit();
        }
        // Incluir el dashboard HTML
        require __DIR__ . '/../views/dashboard.html';
        break;

    case '/login':
        // Si el usuario ya está logueado, redirigir al dashboard
        if (isUserLoggedIn()) {
            header('Location: ' . BASE_URL . '/dashboard');
            exit();
        }
        require __DIR__ . '/../views/login.html';
        break;

    case '/register':
        // Si el usuario ya está logueado, redirigir al dashboard
        if (isUserLoggedIn()) {
            header('Location: ' . BASE_URL . '/dashboard');
            exit();
        }
        require __DIR__ . '/../views/register.html';
        break;

    // Rutas de la API (manejo de datos JSON)
    case '/api/auth':
    case '/api/transactions':
    case '/api/categories':
    case '/api/reports':
        // Para rutas de la API, verificar que el usuario esté autenticado
        // excepto para las acciones de registro y login de la API de auth
        if (strpos($path, '/api/auth') === 0) {
            // La API de auth tiene su propia lógica de autenticación
            require __DIR__ . '/api/auth.php';
        } else {
            // Para otras APIs, requerir que el usuario esté logueado
            if (!isUserLoggedIn()) {
                http_response_code(401); // Unauthorized
                echo json_encode(['success' => false, 'message' => 'Acceso no autorizado. Inicia sesión.']);
                exit();
            }
            // Incluir el archivo de la API correspondiente
            $api_file = __DIR__ . $path . '.php'; // El path ya incluye /api/
            if (file_exists($api_file)) {
                require $api_file;
            } else {
                http_response_code(404); // Not Found
                echo json_encode(['success' => false, 'message' => 'API no encontrada.']);
            }
        }
        break;

    // Rutas para archivos estáticos (CSS, JS)
    // El servidor web (Apache/Nginx) debería manejar esto directamente,
    // pero esta es una forma de fallback si el servidor no está configurado para ello.
    case preg_match('/^\/(css|js)\/(.*)$/', $path, $matches) ? true : false:
        $file_type = $matches[1];
        $file_name = $matches[2];
        $file_path = __DIR__ . '/' . $file_type . '/' . $file_name;

        if (file_exists($file_path)) {
            // Establecer el tipo de contenido adecuado
            $mime_types = [
                'css' => 'text/css',
                'js' => 'application/javascript',
            ];
            header('Content-Type: ' . ($mime_types[$file_type] ?? 'application/octet-stream'));
            readfile($file_path);
        } else {
            http_response_code(404);
            echo "Archivo estático no encontrado.";
        }
        break;

    default:
        http_response_code(404); // Not Found
        echo "404 - Página no encontrada.";
        break;
}