<?php
/**
 * Módulo de Manual de Procedimientos
 */
define('BASE_PATH', dirname(dirname(dirname(__FILE__))));
require_once BASE_PATH . '/config/db_config.php';
require_once INCLUDES_PATH . '/init.php';

if (!Auth::isAuthenticated()) {
    Session::redirect('public/login.php');
}

$page_title = 'Manual de Procedimientos';

// Obtener manuales
$query = "SELECT * FROM manuales WHERE estado = 1 ORDER BY fecha_creacion DESC";
$result = $db->query($query);
$manuales = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $manuales[] = $row;
    }
}

include BASE_PATH . '/views/layout/header.php';
include BASE_PATH . '/views/layout/sidebar.php';
?>

<main class="main-content">
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="page-title"><i class="fas fa-book-open"></i> Manual de Procedimientos</h1>
                <p class="text-muted">Acceso de sólo lectura al Manual de Procedimientos del Tribunal</p>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <?php if (count($manuales) > 0): ?>
                    <div class="accordion" id="manualAccordion">
                        <?php foreach ($manuales as $index => $manual): ?>
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button <?php echo $index === 0 ? '' : 'collapsed'; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#manual<?php echo $manual['id']; ?>">
                                        <i class="fas fa-file-alt me-2"></i> <?php echo Utils::sanitize($manual['titulo']); ?>
                                    </button>
                                </h2>
                                <div id="manual<?php echo $manual['id']; ?>" class="accordion-collapse collapse <?php echo $index === 0 ? 'show' : ''; ?>" data-bs-parent="#manualAccordion">
                                    <div class="accordion-body">
                                        <?php if (!empty($manual['descripcion'])): ?>
                                            <div class="mb-3">
                                                <strong>Descripción:</strong>
                                                <p class="text-muted"><?php echo Utils::sanitize($manual['descripcion']); ?></p>
                                            </div>
                                        <?php endif; ?>
                                        <hr>
                                        <div class="manual-content" style="max-height: 600px; overflow-y: auto; padding: 1rem; background: #f9f9f9; border-radius: 6px;">
                                            <?php echo nl2br(Utils::sanitize(substr($manual['contenido'], 0, 2000))); ?>
                                            <?php if (strlen($manual['contenido']) > 2000): ?>
                                                ...
                                            <?php endif; ?>
                                        </div>
                                        <div class="mt-3">
                                            <small class="text-muted">
                                                <i class="fas fa-calendar"></i> Última actualización: <?php echo Utils::formatDateTime($manual['fecha_actualizacion']); ?>
                                            </small>
                                        </div>
                                        <?php if (!empty($manual['archivo_pdf'])): ?>
                                            <div class="mt-3">
                                                <a href="<?php echo UPLOADS_URL; ?>/<?php echo Utils::sanitize($manual['archivo_pdf']); ?>" class="btn btn-sm btn-danger" download>
                                                    <i class="fas fa-download"></i> Descargar PDF
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> No hay manuales disponibles en este momento.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<?php include BASE_PATH . '/views/layout/footer.php'; ?>
