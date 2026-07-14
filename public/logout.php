<?php
/**
 * Logout
 */
define('BASE_PATH', dirname(dirname(__FILE__)));
define('APP_URL', 'http://localhost/intranet_jltiqq');
define('INCLUDES_PATH', BASE_PATH . '/includes');

session_start();

require_once BASE_PATH . '/config/Database.php';
require_once INCLUDES_PATH . '/Auth.php';
require_once INCLUDES_PATH . '/Session.php';

Auth::logout();
header('Location: ' . APP_URL . '/public/login.php?logout=1');
exit();
?>
