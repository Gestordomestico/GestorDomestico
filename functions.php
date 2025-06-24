<?php
// functions.php
// Ubicación: C:\laragon\www\gestor_domestico_mvp\functions.php

/**
 * Función para verificar si el usuario está logueado.
 * Requiere que session_start() se haya llamado previamente.
 * @return bool True si el usuario está logueado, false en caso contrario.
 */
function isUserLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Aplica medidas de seguridad a la sesión para prevenir secuestro y fijación.
 * @param int $timeout Duración máxima de inactividad en segundos (ej: 1800 para 30 minutos).
 */
function sessionSecurityCheck($timeout = 1800) { // 30 minutos
    if (session_status() == PHP_SESSION_NONE) {
        session_start(); // Asegurarse de que la sesión esté iniciada
    }

    // OWASP: Protección contra secuestro de sesión y fijación
    // Regenerar el ID de sesión periódicamente para aumentar la seguridad.
    // También se regenera al iniciar sesión y cerrar sesión explícitamente.
    if (!isset($_SESSION['CREATED'])) {
        $_SESSION['CREATED'] = time();
        session_regenerate_id(true); // Regenerar y eliminar el antiguo ID de sesión
    } elseif (time() - $_SESSION['CREATED'] > 600) { // Regenerar cada 10 minutos (600 segundos)
        session_regenerate_id(true);
        $_SESSION['CREATED'] = time();
    }

    // OWASP: Verificación de actividad de la sesión (tiempo de inactividad)
    if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $timeout)) {
        // Sesión inactiva por demasiado tiempo
        session_unset();     // Desestablecer todas las variables de sesión
        session_destroy();   // Destruir la sesión
        // Borrar la cookie de sesión del navegador
        setcookie(session_name(), '', time() - 3600, '/');
        // Redirigir al login si la sesión ha expirado
        // Asegúrate de que BASE_URL esté definido en config.php y cargado
        if (defined('BASE_URL')) {
            header('Location: ' . BASE_URL . '/login?message=session_expired');
        } else {
            // Fallback si BASE_URL no está definido por algún motivo (ej. error en config.php)
            header('Location: /login?message=session_expired');
        }
        exit();
    }
    $_SESSION['LAST_ACTIVITY'] = time(); // Actualizar tiempo de última actividad

    // OWASP: Verificación del User Agent para detectar secuestro de sesión
    // Esto es una capa adicional, pero puede causar problemas con proxies o ciertos navegadores.
    // Es una buena práctica, pero monitorea si causa problemas.
    $current_user_agent_hash = md5($_SERVER['HTTP_USER_AGENT'] ?? '');
    if (!isset($_SESSION['HTTP_USER_AGENT'])) {
        $_SESSION['HTTP_USER_AGENT'] = $current_user_agent_hash;
    } elseif ($_SESSION['HTTP_USER_AGENT'] !== $current_user_agent_hash) {
        // User Agent ha cambiado, posible secuestro de sesión
        session_unset();
        session_destroy();
        setcookie(session_name(), '', time() - 3600, '/');
        if (defined('BASE_URL')) {
            header('Location: ' . BASE_URL . '/login?message=session_hijacked');
        } else {
            header('Location: /login?message=session_hijacked');
        }
        exit();
    }
}

/**
 * Función para sanear la salida HTML y prevenir ataques XSS.
 * Siempre usar antes de mostrar cualquier dato de usuario en HTML.
 * @param string $data Los datos a sanear.
 * @return string Los datos saneados.
 */
function sanitizeHtmlOutput($data) {
    return htmlspecialchars((string)$data, ENT_QUOTES, 'UTF-8');
}

/**
 * Valida un formato de fecha 'AAAA-MM-DD'.
 * @param string $date La cadena de fecha.
 * @return bool True si la fecha es válida y existe, false en caso contrario.
 */
function isValidDate($date) {
    if (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $date)) {
        return false;
    }
    // Verificar si la fecha es realmente una fecha válida
    list($year, $month, $day) = explode('-', $date);
    return checkdate((int)$month, (int)$day, (int)$year);
}

/**
 * Verifica si una categoría existe para un tipo dado en la base de datos.
 * Esto es para validar las categorías que ingresa el usuario en transacciones.
 * @param string $category_name Nombre de la categoría.
 * @param string $category_type 'income' o 'expense'.
 * @param PDO $db Objeto PDO de la base de datos.
 * @return bool True si la categoría existe para el tipo, false en caso contrario.
 */
function isValidCategory($category_name, $category_type, PDO $db) {
    try {
        $stmt = $db->prepare("SELECT COUNT(*) FROM categories WHERE name = ? AND type = ?");
        $stmt->execute([$category_name, $category_type]);
        return $stmt->fetchColumn() > 0;
    } catch (PDOException $e) {
        error_log("Error al verificar categoría en DB: " . $e->getMessage());
        return false; // En caso de error de DB, se considera inválida por seguridad
    }
}

/**
 * Valida y filtra IDs.
 * @param mixed $id El ID a validar.
 * @return int|false El ID como entero positivo si es válido, o false si no.
 */
function validateId($id) {
    $filtered_id = filter_var($id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    return $filtered_id === false ? false : (int)$filtered_id;
}

/**
 * Valida y filtra tipos de transacción/categoría.
 * @param string $type El tipo a validar.
 * @return string|false El tipo si es 'income' o 'expense', o false.
 */
function validateType($type) {
    if (in_array($type, ['income', 'expense'])) {
        return $type;
    }
    return false;
}

/**
 * Valida y filtra montos.
 * @param mixed $amount El monto a validar.
 * @return float|false El monto como float positivo si es válido, o false.
 */
function validateAmount($amount) {
    // Permite hasta 2 decimales, máximo 99999999.99
    $filtered_amount = filter_var($amount, FILTER_VALIDATE_FLOAT);
    if ($filtered_amount === false || $filtered_amount <= 0 || $filtered_amount > 99999999.99) {
        return false;
    }
    // Asegurarse de que no haya más de 2 decimales para evitar problemas con la base de datos
    // y para consistencia en la lógica de negocio.
    if (round($filtered_amount * 100) / 100 !== $filtered_amount) {
        return false; // Más de dos decimales
    }
    return $filtered_amount;
}

/**
 * Valida y sanea descripciones/nombres con límites de longitud.
 * @param string $text El texto a sanear.
 * @param int $maxLength La longitud máxima permitida.
 * @return string El texto saneado, o false si excede la longitud.
 */
function validateString($text, $maxLength) {
    $sanitized_text = trim($text); // Eliminar espacios al inicio/fin
    if (mb_strlen($sanitized_text, 'UTF-8') > $maxLength) {
        return false;
    }
    // Opcional: Podrías añadir más filtrado aquí si los requisitos son muy específicos
    // Por ejemplo, para nombres de usuario, permitir solo alfanuméricos.
    return $sanitized_text;
}