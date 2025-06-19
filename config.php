<?php
// config.php

require_once 'functions.php'; // Incluye las funciones de utilidad

// Cargar variables de entorno del archivo .env
$dotenv_path = __DIR__ . '/.env';
if (file_exists($dotenv_path)) {
    $lines = file($dotenv_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($name, $value) = explode('=', $line, 2);
            $_ENV[trim($name)] = trim($value);
            $_SERVER[trim($name)] = trim($value);
        }
    }
}

// Ruta absoluta al archivo SQLite
$db_absolute_path = realpath(__DIR__ . '/' . ($_ENV['DB_PATH'] ?? ''));

if (!$db_absolute_path || !file_exists($db_absolute_path)) {
    error_log("Error: DB_PATH no está definido en .env o el archivo SQLite no existe en " . $db_absolute_path);
    jsonResponse(['error' => 'Error de configuración de la base de datos. Contacte al administrador.'], 500);
}

try {
    // Conexión PDO a SQLite
    $pdo = new PDO('sqlite:' . $db_absolute_path);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO_ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO_FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error de conexión a la base de datos: " . $e->getMessage());
    jsonResponse(['error' => 'No se pudo conectar a la base de datos. Intente de nuevo más tarde.'], 500);
}

// Iniciar sesión para gestionar autenticación
startSecureSession();
?>