<?php
/**
 * Módulo de Decretos Económicos
 */
define('BASE_PATH', dirname(dirname(dirname(__FILE__))));
require_once BASE_PATH . '/config/db_config.php';
require_once INCLUDES_PATH . '/init.php';

if (!Auth::isAuthenticated()) {
    Session::redirect('public/login.php');
}

$page_title = 'Decretos Económicos';

// Páginación
$por_pagina = 15;
$pagina = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($pagina - 1) * $por_pagina;

// Total
$result_total = $db->query("SELECT COUNT(*) as total FROM decretos WHERE estado = 1");
$row_total = $result_total->fetch_assoc();
$total_decretos = $row_total['total'];
$total_paginas = ceil($total_decretos / $por_pagina);

// Obtener decretos
$query = "SELECT * FROM decretos WHERE estado = 1 ORDER BY fecha_decreto DESC LIMIT $offset, $por_pagina";
$result = $db->query($query);
$decretos = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $decretos[] = $row;
    }
}

include BASE_PATH . '/views/layout/header.php';
include BASE_PATH . '/views/layout/sidebar.php';
?>

<main class="main-content">
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="page-title"><i class="fas fa-file-pdf"></i> Decretos Económicos</h1>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <?php if (count($decretos) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Número</th>
                                            <th>Fecha</th>
                                            <th>Contenido</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($decretos as $decreto): ?>
                                            <tr>
                                                <td><?php echo Utils::sanitize($decreto['numero_decreto']); ?></td>
                                                <td><?php echo Utils::formatDate($decreto['fecha_decreto'], 'd/m/Y'); ?></td>
                                                <td><?php echo Utils::sanitize(Utils::truncate($decreto['contenido'], 100)); ?></td>
                                                <td>
                                                    <div class="action-buttons">
                                                        <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#modalDecreto<?php echo $decreto['id']; ?>">
                                                            <i class="fas fa-eye"></i> Ver
                                                        </button>
                                                        <?php if (!empty($decreto['archivo_pdf'])): ?>
                                                            <a href="<?php echo UPLOADS_URL; ?>/<?php echo Utils::sanitize($decreto['archivo_pdf']); ?>" class="btn btn-sm btn-danger" download>
                                                                <i class="fas fa-download"></i> PDF
                                                            </a>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                            </tr>

                                            <!-- Modal -->
                                            <div class="modal fade" id="modalDecreto<?php echo $decreto['id']; ?>" tabindex="-1">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Decreto <?php echo Utils::sanitize($decreto['numero_decreto']); ?></h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p><strong>Fecha:</strong> <?php echo Utils::formatDate($decreto['fecha_decreto'], 'd/m/Y'); ?></p>
                                                            <hr>
                                                            <div><?php echo nl2br(Utils::sanitize($decreto['contenido'])); ?></div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Páginación -->
                            <?php if ($total_paginas > 1): ?>
                                <nav aria-label="Paginación" class="mt-4">
                                    <ul class="pagination justify-content-center">
                                        <?php if ($pagina > 1): ?>
                                            <li class="page-item"><a class="page-link" href="?page=1"><i class="fas fa-chevron-left"></i> Primera</a></li>
                                            <li class="page-item"><a class="page-link" href="?page=<?php echo $pagina - 1; ?>">Anterior</a></li>
                                        <?php endif; ?>

                                        <?php for ($i = max(1, $pagina - 2); $i <= min($total_paginas, $pagina + 2); $i++): ?>
                                            <li class="page-item <?php echo $i == $pagina ? 'active' : ''; ?>">
                                                <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                            </li>
                                        <?php endfor; ?>

                                        <?php if ($pagina < $total_paginas): ?>
                                            <li class="page-item"><a class="page-link" href="?page=<?php echo $pagina + 1; ?>">Siguiente</a></li>
                                            <li class="page-item"><a class="page-link" href="?page=<?php echo $total_paginas; ?>">Ultima <i class="fas fa-chevron-right"></i></a></li>
                                        <?php endif; ?>
                                    </ul>
                                </nav>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> No hay decretos registrados.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include BASE_PATH . '/views/layout/footer.php'; ?>
