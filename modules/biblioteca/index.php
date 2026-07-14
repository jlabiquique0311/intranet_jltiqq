<?php
/**
 * Módulo de Biblioteca de Documentos
 */
define('BASE_PATH', dirname(dirname(dirname(__FILE__))));
require_once BASE_PATH . '/config/db_config.php';
require_once INCLUDES_PATH . '/init.php';

if (!Auth::isAuthenticated()) {
    Session::redirect('public/login.php');
}

$page_title = 'Biblioteca de Documentos';

// Parámetros
$categoria = isset($_GET['categoria']) ? $_GET['categoria'] : '';
$pagina = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$por_pagina = 12;

// Construir query
$where = "WHERE estado = 1";
if (!empty($categoria)) {
    $where .= " AND categoria = '" . $db->escape($categoria) . "'";
}

// Obtener categorías
$categorias = [];
$result = $db->query("SELECT DISTINCT categoria FROM biblioteca WHERE estado = 1 ORDER BY categoria");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $categorias[] = $row['categoria'];
    }
}

// Total
$result_total = $db->query("SELECT COUNT(*) as total FROM biblioteca $where");
$row_total = $result_total->fetch_assoc();
$total = $row_total['total'];
$total_paginas = ceil($total / $por_pagina);

// Obtener documentos
$offset = ($pagina - 1) * $por_pagina;
$query = "SELECT * FROM biblioteca $where ORDER BY fecha_carga DESC LIMIT $offset, $por_pagina";
$result = $db->query($query);
$documentos = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $documentos[] = $row;
    }
}

include BASE_PATH . '/views/layout/header.php';
include BASE_PATH . '/views/layout/sidebar.php';
?>

<main class="main-content">
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="page-title"><i class="fas fa-book"></i> Biblioteca de Documentos</h1>
            </div>
        </div>

        <div class="row">
            <!-- Filtro de categorías -->
            <div class="col-lg-3 mb-4">
                <div class="card">
                    <div class="card-header bg-gradient-blue">
                        <h5 class="card-title mb-0"><i class="fas fa-filter"></i> Categorías</h5>
                    </div>
                    <div class="card-body">
                        <a href="?" class="btn btn-sm btn-outline-primary w-100 mb-2 <?php echo empty($categoria) ? 'active' : ''; ?>">
                            Todas
                        </a>
                        <?php foreach ($categorias as $cat): ?>
                            <a href="?categoria=<?php echo urlencode($cat); ?>" class="btn btn-sm btn-outline-primary w-100 mb-2 <?php echo $categoria === $cat ? 'active' : ''; ?>">
                                <?php echo Utils::sanitize($cat); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Documentos -->
            <div class="col-lg-9">
                <?php if (count($documentos) > 0): ?>
                    <div class="row">
                        <?php foreach ($documentos as $doc): ?>
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card h-100" style="border-top: 3px solid #003d7a;">
                                    <div class="card-body">
                                        <h6 class="card-title"><?php echo Utils::sanitize(Utils::truncate($doc['titulo'], 50)); ?></h6>
                                        <p class="card-text text-muted" style="font-size: 0.85rem;"><?php echo Utils::sanitize(Utils::truncate($doc['descripcion'], 80)); ?></p>
                                        <small class="text-muted d-block mb-2">
                                            <i class="fas fa-calendar"></i> <?php echo Utils::formatDate($doc['fecha_carga'], 'd/m/Y'); ?>
                                        </small>
                                        <small class="text-muted d-block mb-3">
                                            <i class="fas fa-tag"></i> <?php echo Utils::sanitize($doc['categoria']); ?>
                                        </small>
                                        <a href="<?php echo UPLOADS_URL; ?>/<?php echo Utils::sanitize($doc['archivo']); ?>" class="btn btn-sm btn-primary w-100" download>
                                            <i class="fas fa-download"></i> Descargar
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Páginación -->
                    <?php if ($total_paginas > 1): ?>
                        <nav aria-label="Paginación" class="mt-4">
                            <ul class="pagination justify-content-center">
                                <?php if ($pagina > 1): ?>
                                    <li class="page-item"><a class="page-link" href="?page=1<?php echo !empty($categoria) ? '&categoria=' . urlencode($categoria) : ''; ?>"><i class="fas fa-chevron-left"></i> Primera</a></li>
                                    <li class="page-item"><a class="page-link" href="?page=<?php echo $pagina - 1; ?><?php echo !empty($categoria) ? '&categoria=' . urlencode($categoria) : ''; ?>">Anterior</a></li>
                                <?php endif; ?>

                                <?php for ($i = max(1, $pagina - 2); $i <= min($total_paginas, $pagina + 2); $i++): ?>
                                    <li class="page-item <?php echo $i == $pagina ? 'active' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $i; ?><?php echo !empty($categoria) ? '&categoria=' . urlencode($categoria) : ''; ?>"><?php echo $i; ?></a>
                                    </li>
                                <?php endfor; ?>

                                <?php if ($pagina < $total_paginas): ?>
                                    <li class="page-item"><a class="page-link" href="?page=<?php echo $pagina + 1; ?><?php echo !empty($categoria) ? '&categoria=' . urlencode($categoria) : ''; ?>">Siguiente</a></li>
                                    <li class="page-item"><a class="page-link" href="?page=<?php echo $total_paginas; ?><?php echo !empty($categoria) ? '&categoria=' . urlencode($categoria) : ''; ?>">Ultima <i class="fas fa-chevron-right"></i></a></li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> No hay documentos disponibles.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<?php include BASE_PATH . '/views/layout/footer.php'; ?>
