<?php
/**
 * Módulo de Cumpleaños
 */
define('BASE_PATH', dirname(dirname(dirname(__FILE__))));
require_once BASE_PATH . '/config/db_config.php';
require_once INCLUDES_PATH . '/init.php';

if (!Auth::isAuthenticated()) {
    Session::redirect('public/login.php');
}

$page_title = 'Cumpleaños';
$view = isset($_GET['view']) ? $_GET['view'] : 'semanal';

// Obtener cumpleaños según vista
if ($view === 'semanal') {
    $query = "SELECT id, nombre, apellido, fecha_nacimiento FROM usuarios WHERE DATE_FORMAT(fecha_nacimiento, '%m-%d') >= DATE_FORMAT(CURDATE(), '%m-%d') AND DATE_FORMAT(fecha_nacimiento, '%m-%d') <= DATE_FORMAT(DATE_ADD(CURDATE(), INTERVAL 7 DAY), '%m-%d') ORDER BY fecha_nacimiento";
} elseif ($view === 'mensual') {
    $query = "SELECT id, nombre, apellido, fecha_nacimiento FROM usuarios WHERE MONTH(fecha_nacimiento) = MONTH(CURDATE()) AND estado = 1 ORDER BY DAY(fecha_nacimiento)";
} else { // anual
    $query = "SELECT id, nombre, apellido, fecha_nacimiento FROM usuarios WHERE estado = 1 ORDER BY MONTH(fecha_nacimiento), DAY(fecha_nacimiento)";
}

$result = $db->query($query);
$cumpleanos = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $row['edad'] = Utils::calcularEdad($row['fecha_nacimiento']);
        $cumpleanos[] = $row;
    }
}

include BASE_PATH . '/views/layout/header.php';
include BASE_PATH . '/views/layout/sidebar.php';
?>

<main class="main-content">
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="page-title"><i class="fas fa-birthday-cake"></i> Cumpleaños</h1>
            </div>
        </div>

        <!-- Vista de selección -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="btn-group" role="group">
                    <a href="?view=semanal" class="btn btn-sm <?php echo $view === 'semanal' ? 'btn-primary' : 'btn-outline-primary'; ?>">
                        <i class="fas fa-calendar-week"></i> Semanal
                    </a>
                    <a href="?view=mensual" class="btn btn-sm <?php echo $view === 'mensual' ? 'btn-primary' : 'btn-outline-primary'; ?>">
                        <i class="fas fa-calendar-alt"></i> Mensual
                    </a>
                    <a href="?view=anual" class="btn btn-sm <?php echo $view === 'anual' ? 'btn-primary' : 'btn-outline-primary'; ?>">
                        <i class="fas fa-calendar"></i> Anual
                    </a>
                </div>
            </div>
        </div>

        <!-- Lista de cumpleaños -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <?php if (count($cumpleanos) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Nombre</th>
                                            <th>Apellido</th>
                                            <th>Fecha de Nacimiento</th>
                                            <th>Edad</th>
                                            <th>Días para Cumpleaños</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($cumpleanos as $cumple): ?>
                                            <tr>
                                                <td>
                                                    <?php if (Utils::esCumpleHoy($cumple['fecha_nacimiento'])): ?>
                                                        <i class="fas fa-gift" style="color: #ffc107;"></i>
                                                    <?php endif; ?>
                                                    <?php echo Utils::sanitize($cumple['nombre']); ?>
                                                </td>
                                                <td><?php echo Utils::sanitize($cumple['apellido']); ?></td>
                                                <td><?php echo Utils::formatDate($cumple['fecha_nacimiento'], 'd/m/Y'); ?></td>
                                                <td>
                                                    <span class="badge bg-info"><?php echo $cumple['edad']; ?> años</span>
                                                </td>
                                                <td>
                                                    <?php $dias = Utils::diasParaCumple($cumple['fecha_nacimiento']); ?>
                                                    <?php if ($dias == 0): ?>
                                                        <span class="badge bg-warning" style="animation: pulse 1s infinite;">HOY</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary"><?php echo $dias; ?> días</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> No hay cumpleaños en este período.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include BASE_PATH . '/views/layout/footer.php'; ?>
