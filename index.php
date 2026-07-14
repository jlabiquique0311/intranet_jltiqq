<?php
/**
 * Punto de entrada principal - Dashboard
 */
define('BASE_PATH', dirname(__FILE__));
require_once BASE_PATH . '/config/db_config.php';
require_once INCLUDES_PATH . '/init.php';

// Verificar autenticación
if (!Auth::isAuthenticated()) {
    Session::redirect('public/login.php');
}

// Obtener configuración de dashboard del usuario
$user_id = Auth::getUserId();
$query = "SELECT * FROM dashboard_config WHERE usuario_id = $user_id";
$result = $db->query($query);
$dashboard_config = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $dashboard_config[] = $row;
    }
}

// Obtener cumpleaños de la semana
$cumples_semana = [];
$query = "SELECT id, nombre, apellido, fecha_nacimiento FROM usuarios WHERE DATE_FORMAT(fecha_nacimiento, '%m-%d') >= DATE_FORMAT(CURDATE(), '%m-%d') AND DATE_FORMAT(fecha_nacimiento, '%m-%d') <= DATE_FORMAT(DATE_ADD(CURDATE(), INTERVAL 7 DAY), '%m-%d') ORDER BY fecha_nacimiento";
$result = $db->query($query);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $cumples_semana[] = $row;
    }
}

// Obtener últimas noticias
$noticias = [];
$query = "SELECT id, titulo, contenido, fecha_creacion FROM noticias WHERE estado = 1 ORDER BY fecha_creacion DESC LIMIT 5";
$result = $db->query($query);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $noticias[] = $row;
    }
}

// Obtener enlaces relevantes destacados
$enlaces = [];
$query = "SELECT id, nombre, url, descripcion, icono, categoria FROM enlaces_relevantes WHERE estado = 1 AND destacado = 1 ORDER BY nombre LIMIT 6";
$result = $db->query($query);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $enlaces[] = $row;
    }
}

include BASE_PATH . '/views/layout/header.php';
include BASE_PATH . '/views/layout/sidebar.php';
?>

<main class="main-content">
    <div class="container-fluid">
        <!-- Bienvenida -->
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="page-title">Bienvenido, <?php echo Utils::sanitize($_SESSION['nombre'] . ' ' . $_SESSION['apellido']); ?></h1>
                <p class="text-muted">Dashboard de <?php echo APP_NAME; ?></p>
            </div>
        </div>

        <!-- Tarjetas de información configurables -->
        <div class="row" id="dashboard-cards">
            <!-- Cumpleaños de la semana -->
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card dashboard-card">
                    <div class="card-header bg-gradient-blue">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-birthday-cake"></i> Cumpleaños de la Semana
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (count($cumples_semana) > 0): ?>
                            <div class="birthdays-list">
                                <?php foreach ($cumples_semana as $cumple): ?>
                                    <div class="birthday-item mb-2 pb-2 border-bottom">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-light rounded-circle d-flex align-items-center justify-content-center me-2">
                                                <i class="fas fa-user-circle"></i>
                                            </div>
                                            <div>
                                                <strong><?php echo Utils::sanitize($cumple['nombre'] . ' ' . $cumple['apellido']); ?></strong>
                                                <br>
                                                <small class="text-muted"><?php echo Utils::formatDate($cumple['fecha_nacimiento'], 'd/m'); ?></small>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-muted">No hay cumpleaños esta semana</p>
                        <?php endif; ?>
                        <a href="<?php echo APP_URL; ?>/modules/cumpleanos/index.php" class="btn btn-sm btn-outline-primary mt-3">Ver todos</a>
                    </div>
                </div>
            </div>

            <!-- Noticias importantes -->
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card dashboard-card">
                    <div class="card-header bg-gradient-blue">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-newspaper"></i> Noticias Importantes
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (count($noticias) > 0): ?>
                            <div class="news-list">
                                <?php foreach ($noticias as $noticia): ?>
                                    <div class="news-item mb-3 pb-2 border-bottom">
                                        <strong class="d-block"><?php echo Utils::sanitize(Utils::truncate($noticia['titulo'], 50)); ?></strong>
                                        <small class="text-muted"><?php echo Utils::formatDate($noticia['fecha_creacion'], 'd/m/Y'); ?></small>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-muted">No hay noticias disponibles</p>
                        <?php endif; ?>
                        <a href="<?php echo APP_URL; ?>/modules/noticias/index.php" class="btn btn-sm btn-outline-primary mt-3">Ver todas</a>
                    </div>
                </div>
            </div>

            <!-- Enlaces relevantes -->
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card dashboard-card">
                    <div class="card-header bg-gradient-blue">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-link"></i> Enlaces Relevantes
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (count($enlaces) > 0): ?>
                            <div class="links-list">
                                <?php foreach ($enlaces as $enlace): ?>
                                    <div class="link-item mb-2">
                                        <a href="<?php echo Utils::sanitize($enlace['url']); ?>" target="_blank" class="text-decoration-none d-flex align-items-center">
                                            <i class="<?php echo Utils::sanitize($enlace['icono']); ?> me-2"></i>
                                            <span><?php echo Utils::sanitize(Utils::truncate($enlace['nombre'], 35)); ?></span>
                                        </a>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-muted">No hay enlaces disponibles</p>
                        <?php endif; ?>
                        <a href="<?php echo APP_URL; ?>/modules/enlaces/index.php" class="btn btn-sm btn-outline-primary mt-3">Ver todos</a>
                    </div>
                </div>
            </div>

            <!-- Sistemas PJUD -->
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card dashboard-card">
                    <div class="card-header bg-gradient-blue">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-link"></i> Sistemas PJUD
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="sistemas-grid">
                            <a href="http://www.laboral.pjud" target="_blank" class="sistema-link">
                                <i class="fas fa-gavel"></i>
                                <span>SITLA</span>
                            </a>
                            <a href="http://www.cobranza.pjud" target="_blank" class="sistema-link">
                                <i class="fas fa-money-bill-wave"></i>
                                <span>SITCO</span>
                            </a>
                            <a href="http://www.civil.pjud" target="_blank" class="sistema-link">
                                <i class="fas fa-file-contract"></i>
                                <span>SITCI</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include BASE_PATH . '/views/layout/footer.php'; ?>
