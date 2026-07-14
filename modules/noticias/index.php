<?php
/**
 * Módulo de Noticias
 */
define('BASE_PATH', dirname(dirname(dirname(__FILE__))));
require_once BASE_PATH . '/config/db_config.php';
require_once INCLUDES_PATH . '/init.php';

if (!Auth::isAuthenticated()) {
    Session::redirect('public/login.php');
}

$page_title = 'Noticias';

// Paginación
$por_pagina = 10;
$pagina = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($pagina - 1) * $por_pagina;

// Total de noticias
$result_total = $db->query("SELECT COUNT(*) as total FROM noticias WHERE estado = 1");
$row_total = $result_total->fetch_assoc();
$total_noticias = $row_total['total'];
$total_paginas = ceil($total_noticias / $por_pagina);

// Obtener noticias
$query = "SELECT n.*, u.nombre, u.apellido FROM noticias n JOIN usuarios u ON n.autor_id = u.id WHERE n.estado = 1 ORDER BY n.fecha_creacion DESC LIMIT $offset, $por_pagina";
$result = $db->query($query);
$noticias = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $noticias[] = $row;
    }
}

include BASE_PATH . '/views/layout/header.php';
include BASE_PATH . '/views/layout/sidebar.php';
?>

<main class="main-content">
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="page-title"><i class="fas fa-newspaper"></i> Noticias</h1>
            </div>
        </div>

        <!-- Lista de noticias en formato blog -->
        <div class="row">
            <div class="col-lg-8">
                <?php if (count($noticias) > 0): ?>
                    <?php foreach ($noticias as $noticia): ?>
                        <article class="card mb-4">
                            <div class="card-body">
                                <h2 class="card-title" style="color: #003d7a;"><?php echo Utils::sanitize($noticia['titulo']); ?></h2>
                                <div class="text-muted mb-3">
                                    <small>
                                        <i class="fas fa-user"></i> <?php echo Utils::sanitize($noticia['nombre'] . ' ' . $noticia['apellido']); ?>
                                        <i class="fas fa-calendar ms-2"></i> <?php echo Utils::formatDateTime($noticia['fecha_creacion'], 'd/m/Y H:i'); ?>
                                    </small>
                                </div>
                                <div class="card-text">
                                    <?php echo nl2br(Utils::sanitize(Utils::truncate($noticia['contenido'], 300))); ?>
                                </div>
                                <?php if (strlen($noticia['contenido']) > 300): ?>
                                    <a href="#" class="btn btn-sm btn-outline-primary mt-3">Leer más</a>
                                <?php endif; ?>
                            </div>
                        </article>
                    <?php endforeach; ?>

                    <!-- Paginación -->
                    <?php if ($total_paginas > 1): ?>
                        <nav aria-label="Paginación">
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
                        <i class="fas fa-info-circle"></i> No hay noticias publicadas.
                    </div>
                <?php endif; ?>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header bg-gradient-blue">
                        <h5 class="card-title mb-0"><i class="fas fa-info-circle"></i> Información</h5>
                    </div>
                    <div class="card-body">
                        <p>Total de noticias: <strong><?php echo $total_noticias; ?></strong></p>
                        <p>Página <strong><?php echo $pagina; ?></strong> de <strong><?php echo $total_paginas; ?></strong></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include BASE_PATH . '/views/layout/footer.php'; ?>
