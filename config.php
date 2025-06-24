<?php
// config.php
// Ubicación: C:\laragon\www\gestor_domestico_mvp\config.php

// Ruta a la base de datos SQLite
// __DIR__ se refiere al directorio donde está config.php
define('DB_PATH', __DIR__ . '/database/gestordomestico.sqlite');

// Definir la base URL para la aplicación web
// Esto es crucial para las rutas en el frontend JavaScript y HTML
// Ajusta 'gestor_domestico_mvp' si tu carpeta de proyecto tiene otro nombre
define('BASE_URL', '/gestor_domestico_mvp/public');

// Configuración de la base de datos (PDO)
$db = null; // Inicializar a null en caso de error
try {
    // Establecer el modo de error de PDO a excepciones para facilitar el manejo de errores
    $db = new PDO('sqlite:' . DB_PATH);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Habilitar el soporte de FOREIGN KEY para SQLite
    $db->exec('PRAGMA foreign_keys = ON;');
} catch (PDOException $e) {
    // En un entorno de producción, registrar el error en un archivo de log
    // y mostrar un mensaje genérico al usuario.
    error_log("Error de conexión a la base de datos: " . $e->getMessage());
    // Para depuración, puedes mostrar el error directamente:
    // die("Error de conexión a la base de datos: " . $e->getMessage());
    http_response_code(500); // Internal Server Error
    echo json_encode(['success' => false, 'message' => 'Error interno del servidor. No se pudo conectar a la base de datos.']);
    exit();
}