<?php
// Configuración de Base de Datos - COPIAR A db_config.php Y COMPLETAR

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'intranet_jlt');
define('DB_PORT', 3306);
define('DB_CHARSET', 'utf8mb4');

// Configuración de la Aplicación
define('APP_NAME', 'Intranet JLT Iquique');
define('APP_URL', 'http://localhost/intranet_jltiqq');
define('APP_TIMEZONE', 'America/Santiago');

// Configuración de Sesión
define('SESSION_TIMEOUT', 3600); // segundos
define('SESSION_NAME', 'jlt_intranet_session');

// Rutas
define('BASE_PATH', dirname(dirname(__FILE__)));
define('INCLUDES_PATH', BASE_PATH . '/includes');
define('MODULES_PATH', BASE_PATH . '/modules');
define('ADMIN_PATH', BASE_PATH . '/admin');
define('UPLOADS_PATH', BASE_PATH . '/uploads');
define('LOGS_PATH', BASE_PATH . '/logs');

// URLs públicas
define('ASSETS_URL', APP_URL . '/assets');
define('UPLOADS_URL', APP_URL . '/uploads');

// Email (opcional para futuras mejoras)
define('MAIL_HOST', 'smtp.pjud.cl');
define('MAIL_PORT', 587);
define('MAIL_USER', '');
define('MAIL_PASS', '');
define('MAIL_FROM', 'noreply@jltiquique.pjud.cl');

?>
