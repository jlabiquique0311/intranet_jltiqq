<?php
/**
 * Módulo de Libreta de Contactos
 */
define('BASE_PATH', dirname(dirname(dirname(__FILE__))));
require_once BASE_PATH . '/config/db_config.php';
require_once INCLUDES_PATH . '/init.php';

if (!Auth::isAuthenticated()) {
    Session::redirect('public/login.php');
}

$page_title = 'Libreta de Contactos';

// Parámetros de búsqueda
$busqueda = isset($_GET['q']) ? trim($_GET['q']) : '';
$letra = isset($_GET['letra']) ? trim($_GET['letra']) : '';
$vista = isset($_GET['vista']) ? $_GET['vista'] : 'tarjetas';
$pagina = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$por_pagina = $vista === 'tabla' ? 20 : 12;

// Construir query
$where = "WHERE estado = 1";

if (!empty($busqueda)) {
    $busqueda_esc = $db->escape($busqueda);
    $where .= " AND (apellido LIKE '%$busqueda_esc%' OR nombre LIKE '%$busqueda_esc%' OR email LIKE '%$busqueda_esc%' OR telefono LIKE '%$busqueda_esc%')";
} elseif (!empty($letra)) {
    $where .= " AND apellido LIKE '" . $db->escape($letra) . "%'";
}

// Total de contactos
$result_total = $db->query("SELECT COUNT(*) as total FROM contactos $where");
$row_total = $result_total->fetch_assoc();
$total_contactos = $row_total['total'];
$total_paginas = ceil($total_contactos / $por_pagina);

// Obtener contactos
$offset = ($pagina - 1) * $por_pagina;
$query = "SELECT * FROM contactos $where ORDER BY apellido, nombre LIMIT $offset, $por_pagina";
$result = $db->query($query);
$contactos = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $contactos[] = $row;
    }
}

$alfabeto = array_merge(['#'], range('A', 'Z'));

include BASE_PATH . '/views/layout/header.php';
include BASE_PATH . '/views/layout/sidebar.php';
?>

<main class="main-content">
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="page-title"><i class="fas fa-address-book"></i> Libreta de Contactos</h1>
            </div>
        </div>

        <!-- Barra de búsqueda -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="admin-toolbar">
                    <div class="search-box flex-grow-1">
                        <form method="GET" action="" class="d-flex gap-2">
                            <input type="text" class="form-control" name="q" placeholder="Buscar por apellido, nombre, email o teléfono..." value="<?php echo Utils::sanitize($busqueda); ?>">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Buscar</button>
                            <a href="?" class="btn btn-outline-secondary"><i class="fas fa-times"></i> Limpiar</a>
                        </form>
                    </div>
                    <div class="btn-group ms-2" role="group">
                        <a href="?vista=tarjetas<?php echo !empty($busqueda) ? '&q=' . urlencode($busqueda) : (!empty($letra) ? '&letra=' . urlencode($letra) : ''); ?>" class="btn btn-sm <?php echo $vista === 'tarjetas' ? 'btn-primary' : 'btn-outline-primary'; ?>">
                            <i class="fas fa-th-large"></i> Tarjetas
                        </a>
                        <a href="?vista=tabla<?php echo !empty($busqueda) ? '&q=' . urlencode($busqueda) : (!empty($letra) ? '&letra=' . urlencode($letra) : ''); ?>" class="btn btn-sm <?php echo $vista === 'tabla' ? 'btn-primary' : 'btn-outline-primary'; ?>">
                            <i class="fas fa-list"></i> Tabla
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtro por letra -->
        <?php if (empty($busqueda)): ?>
            <div class="row mb-3">
                <div class="col-12">
                    <div class="d-flex flex-wrap gap-2">
                        <?php foreach ($alfabeto as $l): ?>
                            <a href="?letra=<?php echo urlencode($l); ?>&vista=<?php echo $vista; ?>" class="btn btn-sm <?php echo $letra === $l ? 'btn-primary' : 'btn-outline-secondary'; ?>">
                                <?php echo $l; ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Vista de tarjetas -->
        <?php if ($vista === 'tarjetas'): ?>
            <div class="row">
                <?php if (count($contactos) > 0): ?>
                    <?php foreach ($contactos as $contacto): ?>
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title" style="color: #003d7a;"><?php echo Utils::sanitize($contacto['nombre'] . ' ' . $contacto['apellido']); ?></h5>
                                    <?php if (!empty($contacto['cargo'])): ?>
                                        <p class="text-muted" style="font-size: 0.9rem;"><?php echo Utils::sanitize($contacto['cargo']); ?></p>
                                    <?php endif; ?>
                                    <hr>
                                    <?php if (!empty($contacto['departamento'])): ?>
                                        <small class="d-block text-muted"><i class="fas fa-building"></i> <?php echo Utils::sanitize($contacto['departamento']); ?></small>
                                    <?php endif; ?>
                                    <?php if (!empty($contacto['email'])): ?>
                                        <small class="d-block text-muted"><i class="fas fa-envelope"></i> <a href="mailto:<?php echo Utils::sanitize($contacto['email']); ?>"><?php echo Utils::sanitize($contacto['email']); ?></a></small>
                                    <?php endif; ?>
                                    <?php if (!empty($contacto['telefono'])): ?>
                                        <small class="d-block text-muted"><i class="fas fa-phone"></i> <?php echo Utils::sanitize($contacto['telefono']); ?></small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="alert alert-info"><i class="fas fa-info-circle"></i> No se encontraron contactos.</div>
                    </div>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <!-- Vista de tabla -->
            <div class="row">
                <div class="col-12">
                    <?php if (count($contactos) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Apellido</th>
                                        <th>Nombre</th>
                                        <th>Email</th>
                                        <th>Teléfono</th>
                                        <th>Departamento</th>
                                        <th>Cargo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($contactos as $contacto): ?>
                                        <tr>
                                            <td><?php echo Utils::sanitize($contacto['apellido']); ?></td>
                                            <td><?php echo Utils::sanitize($contacto['nombre']); ?></td>
                                            <td>
                                                <?php if (!empty($contacto['email'])): ?>
                                                    <a href="mailto:<?php echo Utils::sanitize($contacto['email']); ?>"><?php echo Utils::sanitize($contacto['email']); ?></a>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if (!empty($contacto['telefono'])): ?>
                                                    <a href="tel:<?php echo urlencode($contacto['telefono']); ?>"><?php echo Utils::sanitize($contacto['telefono']); ?></a>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo !empty($contacto['departamento']) ? Utils::sanitize($contacto['departamento']) : '-'; ?></td>
                                            <td><?php echo !empty($contacto['cargo']) ? Utils::sanitize($contacto['cargo']) : '-'; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info"><i class="fas fa-info-circle"></i> No se encontraron contactos.</div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Paginación -->
        <?php if ($total_paginas > 1): ?>
            <nav aria-label="Paginación" class="mt-4">
                <ul class="pagination justify-content-center">
                    <?php if ($pagina > 1): ?>
                        <li class="page-item"><a class="page-link" href="?page=1&vista=<?php echo $vista; ?><?php echo !empty($busqueda) ? '&q=' . urlencode($busqueda) : (!empty($letra) ? '&letra=' . urlencode($letra) : ''); ?>"><i class="fas fa-chevron-left"></i> Primera</a></li>
                        <li class="page-item"><a class="page-link" href="?page=<?php echo $pagina - 1; ?>&vista=<?php echo $vista; ?><?php echo !empty($busqueda) ? '&q=' . urlencode($busqueda) : (!empty($letra) ? '&letra=' . urlencode($letra) : ''); ?>">Anterior</a></li>
                    <?php endif; ?>

                    <?php for ($i = max(1, $pagina - 2); $i <= min($total_paginas, $pagina + 2); $i++): ?>
                        <li class="page-item <?php echo $i == $pagina ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?>&vista=<?php echo $vista; ?><?php echo !empty($busqueda) ? '&q=' . urlencode($busqueda) : (!empty($letra) ? '&letra=' . urlencode($letra) : ''); ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($pagina < $total_paginas): ?>
                        <li class="page-item"><a class="page-link" href="?page=<?php echo $pagina + 1; ?>&vista=<?php echo $vista; ?><?php echo !empty($busqueda) ? '&q=' . urlencode($busqueda) : (!empty($letra) ? '&letra=' . urlencode($letra) : ''); ?>">Siguiente</a></li>
                        <li class="page-item"><a class="page-link" href="?page=<?php echo $total_paginas; ?>&vista=<?php echo $vista; ?><?php echo !empty($busqueda) ? '&q=' . urlencode($busqueda) : (!empty($letra) ? '&letra=' . urlencode($letra) : ''); ?>">Ultima <i class="fas fa-chevron-right"></i></a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        <?php endif; ?>
    </div>
</main>

<?php include BASE_PATH . '/views/layout/footer.php'; ?>
