<?php
/**
 * Página de Login
 */
define('BASE_PATH', dirname(dirname(__FILE__)));
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'intranet_jlt');
define('DB_PORT', 3306);
define('DB_CHARSET', 'utf8mb4');
define('APP_NAME', 'Intranet JLT Iquique');
define('APP_URL', 'http://localhost/intranet_jltiqq');
define('APP_TIMEZONE', 'America/Santiago');
define('SESSION_TIMEOUT', 3600);
define('INCLUDES_PATH', BASE_PATH . '/includes');

session_start();
date_default_timezone_set(APP_TIMEZONE);

// Incluir clases necesarias
require_once BASE_PATH . '/config/Database.php';
require_once INCLUDES_PATH . '/Auth.php';
require_once INCLUDES_PATH . '/Utils.php';

$error = '';
$message = '';

if (!file_exists(BASE_PATH . '/config/db_config.php')) {
    $error = 'Archivo de configuración no encontrado. Por favor ejecuta la instalación.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($error)) {
    $usuario = isset($_POST['usuario']) ? trim($_POST['usuario']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if (!empty($usuario) && !empty($password)) {
        $db = new Database();
        Auth::setDB($db);

        if (Auth::login($usuario, $password)) {
            header('Location: ' . APP_URL . '/index.php');
            exit();
        } else {
            $error = 'Usuario o contraseña incorrectos';
        }
    } else {
        $error = 'Por favor completa todos los campos';
    }
}

if (isset($_GET['msg']) && $_GET['msg'] === 'sesion_expirada') {
    $message = 'Tu sesión ha expirado. Por favor inicia sesión nuevamente.';
}

if (Auth::isAuthenticated()) {
    header('Location: ' . APP_URL . '/index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/assets/css/login.css">
</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-box">
            <div class="logo-section">
                <div class="logo-circle">
                    <i class="fas fa-gavel"></i>
                </div>
                <h2><?php echo APP_NAME; ?></h2>
                <p class="subtitle">Sistema de Intranet</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Error:</strong> <?php echo Utils::sanitize($error); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (!empty($message)): ?>
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <?php echo Utils::sanitize($message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <form method="POST" action="login.php" class="login-form">
                <div class="mb-3">
                    <label for="usuario" class="form-label">Usuario</label>
                    <input type="text" class="form-control" id="usuario" name="usuario" required autofocus>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>

                <button type="submit" class="btn btn-primary w-100">Iniciar Sesión</button>
            </form>

            <div class="login-footer mt-4">
                <p class="text-muted">Usuarios de prueba:</p>
                <small>
                    Admin: <code>admin</code> / <code>admin123</code><br>
                    Operador: <code>operador</code> / <code>operador123</code>
                </small>
            </div>
        </div>
    </div>

    <script src="<?php echo APP_URL; ?>/assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
