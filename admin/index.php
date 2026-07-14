<?php
/**
 * Panel de Administración - Dashboard
 */
define('BASE_PATH', dirname(dirname(__FILE__)));
require_once BASE_PATH . '/config/db_config.php';
require_once INCLUDES_PATH . '/init.php';

if (!Auth::checkPermission('admin')) {
    Session::redirect('index.php');
}

$page_title = 'Administración';

// Obtener estadísticas
$stats = [];

// Usuarios
$result = $db->query("SELECT COUNT(*) as total FROM usuarios");
$row = $result->fetch_assoc();
$stats['usuarios'] = $row['total'];

// Noticias
$result = $db->query("SELECT COUNT(*) as total FROM noticias WHERE estado = 1");
$row = $result->fetch_assoc();
$stats['noticias'] = $row['total'];

// Contactos
$result = $db->query("SELECT COUNT(*) as total FROM contactos WHERE estado = 1");
$row = $result->fetch_assoc();
$stats['contactos'] = $row['total'];

// Enlaces
$result = $db->query("SELECT COUNT(*) as total FROM enlaces_relevantes WHERE estado = 1");
$row = $result->fetch_assoc();
$stats['enlaces'] = $row['total'];

include BASE_PATH . '/views/layout/header.php';
include BASE_PATH . '/views/layout/sidebar.php';
?>

<main class="main-content">
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="page-title"><i class="fas fa-cog"></i> Panel de Administración</h1>
                <p class="text-muted">Gestión centralizada del sistema</p>
            </div>
        </div>

        <!-- Estadísticas -->
        <div class="row mb-4">
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card">
                    <div class="card-body text-center">
                        <div style="font-size: 2.5rem; color: #003d7a; margin-bottom: 1rem;">
                            <i class="fas fa-users"></i>
                        </div>
                        <h6 class="text-muted">Usuarios</h6>
                        <h4 style="color: #003d7a;"><?php echo $stats['usuarios']; ?></h4>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card">
                    <div class="card-body text-center">
                        <div style="font-size: 2.5rem; color: #003d7a; margin-bottom: 1rem;">
                            <i class="fas fa-newspaper"></i>
                        </div>
                        <h6 class="text-muted">Noticias</h6>
                        <h4 style="color: #003d7a;"><?php echo $stats['noticias']; ?></h4>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card">
                    <div class="card-body text-center">
                        <div style="font-size: 2.5rem; color: #003d7a; margin-bottom: 1rem;">
                            <i class="fas fa-address-book"></i>
                        </div>
                        <h6 class="text-muted">Contactos</h6>
                        <h4 style="color: #003d7a;"><?php echo $stats['contactos']; ?></h4>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card">
                    <div class="card-body text-center">
                        <div style="font-size: 2.5rem; color: #003d7a; margin-bottom: 1rem;">
                            <i class="fas fa-link"></i>
                        </div>
                        <h6 class="text-muted">Enlaces</h6>
                        <h4 style="color: #003d7a;"><?php echo $stats['enlaces']; ?></h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- Menú de opciones -->
        <div class="row">
            <div class="col-lg-8 offset-lg-2">
                <div class="card">
                    <div class="card-header bg-gradient-blue">
                        <h5 class="card-title mb-0"><i class="fas fa-tools"></i> Opciones de Administración</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <a href="<?php echo APP_URL; ?>/admin/usuarios/index.php" class="btn btn-outline-primary w-100 py-3">
                                    <i class="fas fa-users"></i><br>
                                    <span>Gestión de Usuarios</span>
                                </a>
                            </div>
                            <div class="col-md-6 mb-3">
                                <a href="<?php echo APP_URL; ?>/admin/noticias/index.php" class="btn btn-outline-primary w-100 py-3">
                                    <i class="fas fa-newspaper"></i><br>
                                    <span>Gestión de Noticias</span>
                                </a>
                            </div>
                            <div class="col-md-6 mb-3">
                                <a href="<?php echo APP_URL; ?>/admin/contactos/index.php" class="btn btn-outline-primary w-100 py-3">
                                    <i class="fas fa-address-book"></i><br>
                                    <span>Gestión de Contactos</span>
                                </a>
                            </div>
                            <div class="col-md-6 mb-3">
                                <a href="<?php echo APP_URL; ?>/admin/decretos/index.php" class="btn btn-outline-primary w-100 py-3">
                                    <i class="fas fa-file-pdf"></i><br>
                                    <span>Gestión de Decretos</span>
                                </a>
                            </div>
                            <div class="col-md-6 mb-3">
                                <a href="<?php echo APP_URL; ?>/admin/enlaces/index.php" class="btn btn-outline-primary w-100 py-3">
                                    <i class="fas fa-link"></i><br>
                                    <span>Gestión de Enlaces</span>
                                </a>
                            </div>
                            <div class="col-md-6 mb-3">
                                <a href="<?php echo APP_URL; ?>/admin/biblioteca/index.php" class="btn btn-outline-primary w-100 py-3">
                                    <i class="fas fa-book"></i><br>
                                    <span>Gestión de Biblioteca</span>
                                </a>
                            </div>
                            <div class="col-md-6 mb-3">
                                <a href="<?php echo APP_URL; ?>/admin/manuales/index.php" class="btn btn-outline-primary w-100 py-3">
                                    <i class="fas fa-book-open"></i><br>
                                    <span>Gestión de Manuales</span>
                                </a>
                            </div>
                            <div class="col-md-6 mb-3">
                                <a href="<?php echo APP_URL; ?>/admin/agenda/index.php" class="btn btn-outline-primary w-100 py-3">
                                    <i class="fas fa-calendar"></i><br>
                                    <span>Gestión de Agenda</span>
                                </a>
                            </div>
                            <div class="col-md-6 mb-3">
                                <a href="<?php echo APP_URL; ?>/admin/base_datos/index.php" class="btn btn-outline-primary w-100 py-3">
                                    <i class="fas fa-database"></i><br>
                                    <span>Gestión de Base de Datos</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include BASE_PATH . '/views/layout/footer.php'; ?>
