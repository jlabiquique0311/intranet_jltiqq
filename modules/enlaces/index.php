<?php
/**
 * Módulo de Enlaces Relevantes
 */
define('BASE_PATH', dirname(dirname(dirname(__FILE__))));
require_once BASE_PATH . '/config/db_config.php';
require_once INCLUDES_PATH . '/init.php';

if (!Auth::isAuthenticated()) {
    Session::redirect('public/login.php');
}

$page_title = 'Enlaces Relevantes';

// Obtener enlaces por categoría
$enlaces_internos = [];
$enlaces_generales = [];

$query = "SELECT * FROM enlaces_relevantes WHERE estado = 1 ORDER BY categoria, nombre";
$result = $db->query($query);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        if ($row['categoria'] === 'enlace_interno') {
            $enlaces_internos[] = $row;
        } else {
            $enlaces_generales[] = $row;
        }
    }
}

include BASE_PATH . '/views/layout/header.php';
include BASE_PATH . '/views/layout/sidebar.php';
?>

<main class="main-content">
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="page-title"><i class="fas fa-link"></i> Enlaces Relevantes</h1>
            </div>
        </div>

        <!-- Enlaces Internos -->
        <div class="row mb-4">
            <div class="col-12">
                <h3 style="color: #003d7a; margin-bottom: 1.5rem;"><i class="fas fa-building"></i> Enlaces Internos PJUD</h3>
            </div>
        </div>

        <div class="row mb-5">
            <?php if (count($enlaces_internos) > 0): ?>
                <?php foreach ($enlaces_internos as $enlace): ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100" style="border-left: 4px solid #003d7a;">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <i class="<?php echo Utils::sanitize($enlace['icono']); ?}"></i>
                                    <?php echo Utils::sanitize($enlace['nombre']); ?>
                                </h5>
                                <p class="card-text text-muted" style="font-size: 0.9rem;"><?php echo Utils::sanitize($enlace['descripcion']); ?></p>
                                <a href="<?php echo Utils::sanitize($enlace['url']); ?>" target="_blank" class="btn btn-sm btn-primary">
                                    <i class="fas fa-external-link-alt"></i> Acceder
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-info"><i class="fas fa-info-circle"></i> No hay enlaces internos disponibles.</div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Enlaces Generales -->
        <div class="row mb-4">
            <div class="col-12">
                <h3 style="color: #003d7a; margin-bottom: 1.5rem;"><i class="fas fa-globe"></i> Enlaces Generales</h3>
            </div>
        </div>

        <div class="row">
            <?php if (count($enlaces_generales) > 0): ?>
                <?php foreach ($enlaces_generales as $enlace): ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100" style="border-left: 4px solid #0052a3;">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <i class="<?php echo Utils::sanitize($enlace['icono']); ?}"></i>
                                    <?php echo Utils::sanitize($enlace['nombre']); ?>
                                </h5>
                                <p class="card-text text-muted" style="font-size: 0.9rem;"><?php echo Utils::sanitize($enlace['descripcion']); ?></p>
                                <a href="<?php echo Utils::sanitize($enlace['url']); ?>" target="_blank" class="btn btn-sm btn-primary">
                                    <i class="fas fa-external-link-alt"></i> Acceder
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-info"><i class="fas fa-info-circle"></i> No hay enlaces generales disponibles.</div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php include BASE_PATH . '/views/layout/footer.php'; ?>
