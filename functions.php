<?php
// functions.php

/**
 * Hashea una contraseña.
 * @param string $password La contraseña en texto plano.
 * @return string El hash de la contraseña.
 */
function hashPassword(string $password): string {
    return password_hash($password, PASSWORD_DEFAULT);
}

/**
 * Verifica una contraseña con un hash.
 * @param string $password La contraseña en texto plano.
 * @param string $hash El hash almacenado.
 * @return bool True si la contraseña coincide, false en caso contrario.
 */
function verifyPassword(string $password, string $hash): bool {
    return password_verify($password, $hash);
}

/**
 * Inicia una sesión segura si aún no está iniciada.
 */
function startSecureSession(): void {
    if (session_status() === PHP_SESSION_NONE) {
        ini_set('session.use_only_cookies', 1);
        ini_set('session.cookie_httponly', 1);
        // ini_set('session.cookie_secure', 1); // Descomentar en producción con HTTPS
        ini_set('session.gc_maxlifetime', 1440);
        ini_set('session.cookie_samesite', 'Lax');
        session_start();
    }
}

/**
 * Verifica si el usuario está autenticado.
 * @return bool True si el usuario está logueado, false en caso contrario.
 */
function isAuthenticated(): bool {
    startSecureSession();
    return isset($_SESSION['user_id']);
}

/**
 * Redirige al usuario.
 * @param string $location URL a la que redirigir.
 */
function redirectTo(string $location): void {
    header("Location: " . $location);
    exit();
}

/**
 * Envía una respuesta JSON.
 * @param array $data Los datos a codificar en JSON.
 * @param int $statusCode El código de estado HTTP.
 */
function jsonResponse(array $data, int $statusCode = 200): void {
    header('Content-Type: application/json');
    http_response_code($statusCode);
    echo json_encode($data);
    exit();
}
?>