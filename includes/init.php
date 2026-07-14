<?php
/**
 * Archivo de inicialización de la aplicación
 */

// Iniciamos sesión
session_start();

// Configuramos zona horaria
date_default_timezone_set(APP_TIMEZONE);

// Incluimos configuración
if (!file_exists(BASE_PATH . '/config/db_config.php')) {
    die('Error: Archivo de configuración no encontrado. Por favor copia config/db_config_example.php a config/db_config.php');
}
require_once BASE_PATH . '/config/db_config.php';

// Incluimos clases principales
require_once BASE_PATH . '/config/Database.php';
require_once BASE_PATH . '/includes/Auth.php';
require_once BASE_PATH . '/includes/Session.php';
require_once BASE_PATH . '/includes/Utils.php';
require_once BASE_PATH . '/includes/Validator.php';

// Inicializamos BD
$db = new Database();

// Verificamos autenticación
Session::verify();

// Headers de seguridad
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Content-Type: text/html; charset=utf-8');

?>
